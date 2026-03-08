<?php

namespace App\Http\Controllers;

use App\Dto\TinkoffInitPaymentDto;
use App\Models\CyclingActivity;
use App\Models\CyclingStation;
use App\Models\Race;
use App\Models\RaceCluster;
use App\Services\AdminTelegram\AdminTelegram;
use App\Services\TinkoffService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RaceController extends Controller
{
    public function calendar(Request $request, ?int $year = null): Response
    {
        $raceTimeFilter = $request->query('race_time_filter') ?? 'upcoming';

        $selectedRaceTypes = $this->normalizeRaceTypes($request->query('race_types', []));
        $selectedRaceRanks = $this->normalizeRaceRanks($request->query('race_ranks', []));
       
        $baseQuery = Race::query()->published();
        if (!empty($selectedRaceTypes)) {
            $baseQuery->where(function ($query) use ($selectedRaceTypes) {
                foreach ($selectedRaceTypes as $raceType) {
                    $query->orWhereJsonContains('race_types', $raceType);
                }
            });
        }
        if (!empty($selectedRaceRanks)) {
            $baseQuery->whereIn('rank', array_map('intval', $selectedRaceRanks));
        }
        if ($raceTimeFilter === 'upcoming') {
            $baseQuery->whereDate('date', '>=', now()->toDateString());
        }

        $startYear = 2025;
        $currentYear = now()->year;
        $availableYears = collect(range($startYear, max($startYear, $currentYear)));

        $selectedYear = $year ?? now()->year;
        if (is_null($year) && $availableYears->isNotEmpty() && !$availableYears->contains($selectedYear)) {
            $selectedYear = (int) $availableYears->last();
        }

        $races = (clone $baseQuery)
            ->whereYear('date', $selectedYear)
            ->orderBy('date')
            ->get();

        $user = $request->user();
        $participatingRaceIds = [];
        if ($user?->isCoffeeRider() && $races->isNotEmpty()) {
            $participatingRaceIds = $user->participatingRaces()
                ->whereIn('races.id', $races->pluck('id'))
                ->pluck('races.id')
                ->all();
        }

        $races->each(function (Race $race) use ($participatingRaceIds) {
            $race->setAttribute('is_participating', in_array($race->id, $participatingRaceIds, true));
        });

        $prevYear = $availableYears
            ->filter(fn (int $availableYear) => $availableYear < $selectedYear)
            ->last();

        $nextYear = $availableYears
            ->first(fn (int $availableYear) => $availableYear > $selectedYear);

        return Inertia::render('races/Calendar', [
            'races' => $races,
            'year' => $selectedYear,
            'prevYear' => $prevYear,
            'nextYear' => $nextYear,
            'selectedRaceTypes' => array_values($selectedRaceTypes),
            'selectedRaceRanks' => array_values($selectedRaceRanks),
            'raceTimeFilter' => $raceTimeFilter,
        ]);
    }

    private function normalizeRaceTypes(mixed $value): array
    {
        $items = is_array($value) ? $value : [$value];
        $allowed = ['gravel', 'road', 'mtb', 'indoor', 'track', 'cyclocross'];

        return array_values(array_filter(
            array_map(static fn ($item) => strtolower(trim((string) $item)), $items),
            static fn (string $item) => in_array($item, $allowed, true),
        ));
    }

    private function normalizeRaceRanks(mixed $value): array
    {
        $items = is_array($value) ? $value : [$value];
        $allowed = ['1', '2', '3'];

        return array_values(array_filter(
            array_map(static fn ($item) => trim((string) $item), $items),
            static fn (string $item) => in_array($item, $allowed, true),
        ));
    }

    public function show(Request $request, Race $race): Response
    {
        abort_unless($race->is_published, 404);

        $race->load('participants:id,name,avatar_url');
        
        // подгружаем кластеры, если это гонка у нас в студии
        if ($race->in_our_studio) {
            $race->load(['clusters' => function ($query) {
                $query->withCount('cyclingActivities');
            }]);
        }
        
        // Делаем всем коферайдерам цену в два раза меньше
        $user = $request->user();
        if ($race->in_our_studio && $user?->isCoffeeRider()) {
            $race->clusters->each(function ($cluster) {
                $cluster->price = (int) ($cluster->price / 2);
            });
        }

        return Inertia::render('races/Show', [
            'race' => $race,
        ]);
    }

    public function participate(Request $request, Race $race): RedirectResponse
    {
        abort_unless($race->is_published, 404);

        $user = $request->user();
        abort_unless($user && $user->isCoffeeRider(), 403);

        $isParticipating = $race->participants()->where('users.id', $user->id)->exists();
        if ($isParticipating) {
            $race->participants()->detach($user->id);
            return back()->with('success', 'Вы удалены из списка участников.');
        }

        $race->participants()->attach($user->id);

        return back()->with('success', 'Вы добавлены в список участников.');
    }

    public function register(Request $request, Race $race, RaceCluster $cluster, AdminTelegram $adminTelegram)
    {
        abort_unless($race->is_published, 404);
        abort_unless($race->in_our_studio, 404);
        abort_unless($cluster->race_id === $race->id, 404);

        if (!$cluster->hasAvailableSlots()) {
            throw ValidationException::withMessages([
                'cluster' => 'В этой стартовой группе больше нет свободных мест.',
            ]);
        }

        $clusterIds = $race->clusters()->pluck('id');

        $existingRegistration = CyclingActivity::where('user_id', auth()->id())
            ->whereIn('race_cluster_id', $clusterIds)
            ->exists();

        if ($existingRegistration) {
            throw ValidationException::withMessages([
                'cluster' => 'Вы уже зарегистрированы на эту гонку.',
            ]);
        }

        $startsAt = Carbon::parse($race->date)->setTimeFromTimeString($cluster->start_time);
        $endsAt = $startsAt->copy()->addMinutes($cluster->duration_minutes);

        $busyStationIds = CyclingActivity::overlaps($startsAt, $endsAt)
            ->pluck('cycling_station_id')
            ->unique();

        $freeStation = CyclingStation::whereNotIn('id', $busyStationIds)->first();

        if (!$freeStation) {
            throw ValidationException::withMessages([
                'cluster' => 'Нет свободных станций на это время.',
            ]);
        }

        $activity = CyclingActivity::create([
            'user_id' => auth()->id(),
            'cycling_station_id' => $freeStation->id,
            'race_cluster_id' => $cluster->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        $adminTelegram->sendRaceRegistrationNotification($activity, $race, $cluster);
        
        $price = $request->user()->isCoffeeRider() ? (int) ($cluster->price / 2) : $cluster->price;
        
        $dto = new TinkoffInitPaymentDto(
            amount: $price,
            failUrl: route('failed-payment'),
            orderId: 'cycling_activity_' . $activity->id,
            successUrl: route('success-payment'),
            notificationUrl: route('tinkoff.handle-cycling-activity-notification'),
            description: "Регистрация на гонку: {$race->name}",
        );

        $payment = app(TinkoffService::class)->initPayment($dto);

        if (empty($payment['PaymentURL'])) {
            Log::channel('payments')->error('RaceController.register: Ошибка инициализации платежа', [
                'cycling_activity_id' => $activity->id,
                'response' => $payment,
            ]);

            $activity->delete();

            return back()->with('error', 'Ошибка запроса к банку');
        }

        return Inertia::location($payment['PaymentURL']);
    }
}
