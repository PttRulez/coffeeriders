<?php

namespace App\Http\Controllers;

use App\Dto\TinkoffInitPaymentDto;
use App\Enums\ServiceType;
use App\Models\CyclingActivity;
use App\Models\CyclingStation;
use App\Models\Race;
use App\Services\AdminTelegram\AdminTelegram;
use App\Services\CouponService;
use App\Services\PricingService;
use App\Services\TinkoffService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CyclingStudioController extends Controller
{
    public function index(Request $request, PricingService $pricing): Response
    {
        $user = $request->user();
        $races = Race::published()->upcoming()->orderBy('date')->get();

        return Inertia::render('cycling-studio/Index', [
            'price' => $user?->hasCyclingActivitiesLeft() > 0 ? 0 : $pricing->baseCyclingPrice($user),
            'races' => $races,
        ]);
    }

    public function edit(CyclingActivity $activity): Response
    {
        Gate::authorize('update', $activity);

        $stations = CyclingStation::all();

        $busyStationIds = CyclingActivity::query()->where(function ($q) use ($activity) {
            $q->where('ends_at', '>', $activity->starts_at)
                ->where('starts_at', '<', $activity->ends_at)
                ->where('id', '<>', $activity->id);
        })->pluck('cycling_station_id')
            ->unique();

        return Inertia::render('cycling-studio/Edit', [
            'activity' => $activity,
            'stations' => $stations->whereNotIn('id', $busyStationIds)->values()
        ]);
    }

    public function store(Request        $request,
                          AdminTelegram  $adminTelegram,
                          PricingService $pricing,
                          CouponService  $couponService): mixed
    {
        $validated = $request->validate([
            'cycling_station_id' => ['required', 'exists:cycling_stations,id'],
            'starts_at' => ['required', 'date'],
            'pay' => ['boolean'],
            'coupon_code' => ['nullable', 'string'],
        ], [
            'cycling_station_id.required' => 'Выберите станцию.',
            'starts_at.required' => 'Укажите время начала.',
        ]);

        $startsAt = Carbon::parse($validated['starts_at']);
        $endsAt = (clone $startsAt)->addHours(2);

        //  На всякий случай проверяем есть ли другие бронирования пересекающиеся по времени
        $hasConflict = CyclingActivity::where('cycling_station_id', $validated['cycling_station_id'])
            ->where(function ($q) use ($startsAt, $endsAt) {
                $q->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            })
            ->exists();

        if ($hasConflict) {
            throw ValidationException::withMessages([
                'starts_at' => 'Станция уже занята в выбранный интервал.',
            ]);
        }

        $activity = CyclingActivity::create([
            'user_id' => auth()->user()->id,
            'cycling_station_id' => $validated['cycling_station_id'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);


        $adminTelegram->sendStudioBookingStoreNotification($activity, $validated['pay'], $validated['coupon_code']);

        // Базовая цена считается на бэке
        $basePrice = $pricing->baseCyclingPrice($request->user());
        $finalPrice = $basePrice;

        // Применяем купон
        if (!empty($validated['pay']) && filled($validated['coupon_code'])) {
            $res = $couponService->apply(
                $validated['coupon_code'],
                $request->user(),
                ServiceType::Cycling,
                $basePrice,
                $activity
            );

            if (!$res['ok']) {
                throw ValidationException::withMessages([
                    'coupon_code' => $res['message'] ?? 'Купон недействителен',
                ]);
            }

            $finalPrice = $res['new_price'];
        }

        // Оплата в тиньке
        if (!empty($validated['pay'])) {
            // Инициализируем платеж у банка по ИТОГОВОЙ цене
            $dto = new TinkoffInitPaymentDto(
                amount: $finalPrice,
                failUrl: route('failed-payment'),
                orderId: 'cycling_activity_' . $activity->id,
                successUrl: route('success-payment'),
                notificationUrl: route('tinkoff.handle-cycling-activity-notification'),
            );

            $payment = app(TinkoffService::class)->initPayment($dto);

            if (empty($payment['PaymentURL'])) {
                Log::channel('payments')->error('CyclingStudioController.storeBooking: Ошибка инициализации платежа в Tinkoff', [
                    'cycling_activity_id' => $activity->id,
                    'response' => $payment,
                ]);
                return back()->with('error', 'Ошибка запроса к банку');
            }

            return Inertia::location($payment['PaymentURL']);
        }

        // Списываем предоплаченные занятия, купон НЕ применяем
        if ($request->user()->paid_cycling_count > 0) {
            $request->user()->decrement('paid_cycling_count');
            $activity->update(['is_paid' => true]);
        }

        return to_route('user-account.index')
            ->with('success', 'Занятие успешно забронировано.');
    }

    public function create(PricingService $pricing): Response
    {
        $user = auth()->user();

        return Inertia::render('cycling-studio/Create', [
            'pricing' => [
                'service' => ServiceType::Cycling,
                'base_price' => $pricing->baseCyclingPrice($user),
                'final_price' => $pricing->baseCyclingPrice($user),
            ],
        ]);
    }

    public function update(Request         $request,
                           CyclingActivity $activity,
                           AdminTelegram   $adminTelegram): mixed
    {
        Gate::authorize('update', $activity);

        $validated = $request->validate([
            'cycling_station_id' => ['required', 'exists:cycling_stations,id'],
            'starts_at' => ['required', 'date'],
        ], [
            'cycling_station_id.required' => 'Выберите станцию.',
            'starts_at.required' => 'Укажите время начала.',
        ]);

        $startsAt = Carbon::parse($validated['starts_at']);
        $endsAt = (clone $startsAt)->addHours(2);

//        if ($activity->starts_at->lte(now()->addHours(6))) {
//            throw ValidationException::withMessages([
//                'starts_at' => 'Бронь можно изменить не позднее чем за 6 часов до начала занятия.',
//            ]);
//        }rf

        //  На всякий случай проверяем есть ли другие бронирования пересекающиеся по времени
        $hasConflict = CyclingActivity::where('cycling_station_id', $validated['cycling_station_id'])
            ->where(function ($q) use ($startsAt, $endsAt, $activity) {
                $q->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt)
                    ->where('id', '<>', $activity->id);
            })
            ->exists();

        if ($hasConflict) {
            throw ValidationException::withMessages([
                'starts_at' => 'Станция уже занята в выбранный интервал.',
            ]);
        }

        $activity->cycling_station_id = $validated['cycling_station_id'];
        $activity->starts_at = $startsAt;
        $activity->ends_at = $endsAt;

        if ($activity->isDirty('starts_at')) {
            $adminTelegram->sendStudioBookingUpdatedNotification($activity);
        }

        $activity->save();
        return to_route('user-account.index')
            ->with('success', 'Сохранено');
    }

    public function destroy(CyclingActivity $activity, AdminTelegram  $adminTelegram): RedirectResponse
    {
        $activity->delete();

        $adminTelegram->sendStudioBookingDeletedNotification($activity);

        return back()->with('success', 'Тренировка удалена');
    }

    public function buyActivities(Request $request): mixed
    {
        $quantity = $request->input('quantity');
        $price = match ($quantity) {
            4 => 5000,
            10 => 10000,
        };

        $cyclingOrder = $request->user()->cyclingOrders()->create(['quantity' => $quantity]);

        $dto = new TinkoffInitPaymentDto(
            amount: $price,
            failUrl: route('failed-payment'),
            orderId: 'cycling_order_' . $cyclingOrder->id,
            successUrl: route('success-payment'),
            notificationUrl: route('tinkoff.handle-cycling-order-notification'),
        );

        $payment = app(TinkoffService::class)->initPayment($dto);

        if (empty($payment['PaymentURL'])) {
            Log::channel('payments')->error('CyclingStudioController.buyActivities Ошибка инициализации платежа в Tinkoff', [
                'cycling_order_id' => $cyclingOrder->id,
                'response' => $payment,
            ]);
            return back()->with('error', 'Ошибка запроса к банку');
        }

        return Inertia::location($payment['PaymentURL']);
    }

    public function bikeCheck(Request $request): JsonResponse
    {
        $start = Carbon::parse($request->input('datetime'));
        $end = $start->copy()->addHours(2);

        $stations = CyclingStation::all();

        $busyStationIds = CyclingActivity::overlaps($start, $end)
            ->pluck('cycling_station_id')
            ->unique();

        $hasBookedAlready = $request->user()->cyclingActivities->where('starts_at', '=', $start)->values();

        $freeStations = $stations->whereNotIn('id', $busyStationIds);

        return response()->json([
            'stations' => $freeStations->values(),
            'has_booked_already' => $hasBookedAlready
        ]);
    }
}
