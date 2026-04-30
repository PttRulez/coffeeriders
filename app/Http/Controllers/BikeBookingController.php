<?php

namespace App\Http\Controllers;

use App\Dto\TinkoffInitPaymentDto;
use App\Enums\BookingStatusEnum;
use App\Http\Requests\CreateBikeBookingRequest;
use App\Models\Bike;
use App\Models\BikeBooking;
use App\Support\TelegramUsernameExtractor;
use App\Services\AdminTelegram\AdminTelegram;
use App\Services\TinkoffService;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use function back;
use function route;

class BikeBookingController extends Controller
{
    public function store(CreateBikeBookingRequest $request, AdminTelegram $adminTelegram, TinkoffService $tinkoff): mixed
    {
        $data = $request->validated();

        if (Carbon::parse($data['starts_at'])->isBefore(today())) {
            return response()->json(['message' => 'Дата начала не может быть в прошлом'], 422);
        }

        $booking = BikeBooking::create([
            'bike_id' => $data['bike_id'],
            'comment' => $data['comment'] ?? null,
            'customer_name' => $data['customer_name'],
            'ends_at' => Carbon::parse($data['ends_at'])->toDateString(),
            'phone' => $data['phone'] ?? null,
            'starts_at' => Carbon::parse($data['starts_at'])->toDateString(),
            'status' => BookingStatusEnum::Booked->value,
            'telegram_username' => $this->normalizeTelegramUsername($data['telegram_username'] ?? null),
        ]);

        $adminTelegram->sendProkatBookingNotification($booking);

        $bike = Bike::findOrFail($data['bike_id']);

        $oneDayPrice = min(array_column($bike->prices, 'price'));

        $dto = new TinkoffInitPaymentDto(
            amount: $oneDayPrice / 2,
            failUrl: route('failed-payment'),
            orderId: 'bike_booking_id_' . $booking->id,
            successUrl: route('success-payment'),
            notificationUrl: route('tinkoff.handle-bike-booking-notification', ['bikeBooking' => $booking->id]),
        );

        $payment = app(TinkoffService::class)->initPayment($dto);

        if (empty($payment['PaymentURL'])) {
            Log::channel('payments')->error('BikeBookingController.store Ошибка инициализации платежа в Tinkoff', [
                'booking_id' => $booking->id,
                'response' => $payment,
            ]);
            return back()->with('error', 'Ошибка запроса к банку');
        }

        return Inertia::location($payment['PaymentURL']);
    }

    private function normalizeTelegramUsername(?string $value): ?string
    {
        return TelegramUsernameExtractor::extract($value);
    }
}
