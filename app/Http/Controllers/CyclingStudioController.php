<?php

namespace App\Http\Controllers;

use App\Dto\TinkoffInitPaymentDto;
use App\Models\CyclingActivity;
use App\Models\CyclingOrder;
use App\Models\CyclingStation;
use App\Services\AdminTelegram\AdminTelegram;
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
    public function index(): Response
    {
        return Inertia::render('cycling-studio/Index');
    }
    
    public function booking(): Response
    {
        return Inertia::render('cycling-studio/Booking');
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
    
    public function storeBooking(Request $request, AdminTelegram $adminTelegram): mixed
    {
        $validated = $request->validate([
            'cycling_station_id' => ['required', 'exists:cycling_stations,id'],
            'starts_at' => ['required', 'date'],
            'pay' => ['boolean'],
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
        
        $price = $request->user()->is_coffeerider ? 750 : 1500;
        
        if ($validated['pay']) {
            // Инициализируем платеж у тинька и редиректим туда
            $dto = new TinkoffInitPaymentDto(
                amount: $price,
                failUrl: route('failed-payment'),
                orderId: 'cycling_activity_' . $activity->id,
                successUrl: route('success-payment'),
                notificationUrl: route('tinkoff.handle-cycling-activity-notification'),
            );
            
            $payment = app(TinkoffService::class)->initPayment($dto);
            
            if (empty($payment['PaymentURL'])) {
                Log::channel('payments')->error('CyclingStudioController.storeBooking Ошибка инициализации платежа в Tinkoff', [
                    'cycling_activity_id' => $activity->id,
                    'response' => $payment,
                ]);
                return back()->with('error', 'Ошибка запроса к банку');
            }
            
            return Inertia::location($payment['PaymentURL']);
        } else {
            // Списываем занятие только если без оплаты и если он есть у юзера на счету
            if ($request->user()->paid_cycling_count > 0) {
                $request->user()->decrement('paid_cycling_count');
            }
            
            return redirect()
                ->route('cycling-studio.index')
                ->with('success', 'Занятие успешно забронировано.');
        }
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