<?php

namespace App\Http\Controllers;

use App\Dto\TinkoffInitPaymentDto;
use App\Enums\ServiceType;
use App\Models\CyclingActivity;
use App\Models\CyclingOrder;
use App\Models\CyclingStation;
use App\Services\AdminTelegram\AdminTelegram;
use App\Services\CouponService;
use App\Services\PricingService;
use App\Services\TinkoffService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CyclingStudioController extends Controller
{
    public function index(Request $request, PricingService $pricing): Response
    {
        $user    = $request->user();
        return Inertia::render('cycling-studio/Index', [
            'price' => $user->hasCyclingActivitiesLeft() > 0 ? 0 : $pricing->baseCyclingPrice($user),
        ]);
    }
    
    public function booking(PricingService $pricing): Response
    {
        $user = auth()->user();
        $service = ServiceType::Cycling;
        
        return Inertia::render('cycling-studio/Booking', [
            'pricing' => [
                'base_price' => $pricing->baseCyclingPrice($user),
                'final_price' => $pricing->baseCyclingPrice($user), // пока без купона
            ],
        ]);
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
    
    public function storeBooking(Request        $request,
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
        
        
        $adminTelegram->sendStudioBookingNotification($activity);
        
        // Базовая цена считается на бэке
        $service = ServiceType::Cycling;
        $basePrice = $pricing->baseCyclingPrice($request->user());
        $finalPrice = $basePrice;
        
        // Применяем купон
        if (!empty($validated['pay']) && filled($validated['coupon_code'])) {
            $res = $couponService->apply(
                $validated['coupon_code'],
                $request->user(),
                $service,
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
        
        return redirect()
            ->route('cycling-studio.index')
            ->with('success', 'Занятие успешно забронировано.');
    }
    
    public function bikeCheck(Request $request): JsonResponse
    {
        $start = Carbon::parse($request->input('datetime'));
        $end = $start->copy()->addHours(2);
        
        $stations = CyclingStation::all();
        
        $busyStationIds = CyclingActivity::where(function ($q) use ($start, $end) {
            $q->where('starts_at', '<', $end)
                ->where('ends_at', '>', $start);
        })
            ->pluck('cycling_station_id')
            ->unique();
        
        $freeStations = $stations->whereNotIn('id', $busyStationIds);
        
        return response()->json([
            'stations' => $freeStations->values(),
        ]);
    }
}