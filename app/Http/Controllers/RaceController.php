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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RaceController extends Controller
{
    public function show(Request $request, Race $race): Response
    {
        abort_unless($race->is_published, 404);

        $race->load(['clusters' => function ($query) {
            $query->withCount('cyclingActivities');
        }]);

        $user = $request->user();
        if ($user?->isCoffeeRider()) {
            $race->clusters->each(function ($cluster) {
                $cluster->price = (int) ($cluster->price / 2);
            });
        }

        return Inertia::render('races/Show', [
            'race' => $race,
        ]);
    }

    public function register(Request $request, Race $race, RaceCluster $cluster, AdminTelegram $adminTelegram)
    {
        abort_unless($race->is_published, 404);
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
