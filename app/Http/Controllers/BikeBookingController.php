<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatusEnum;
use App\Http\Requests\CreateBikeBookingRequest;
use App\Models\BikeBooking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class BikeBookingController extends Controller
{
    public function store(CreateBikeBookingRequest $request): mixed
    {
        $data = $request->validated();

        if (Carbon::parse($data['starts_at'])->isBefore(today())) {
            return response()->json(['message' => 'Дата начала не может быть в прошлом'], 422);
        }

        $booking = BikeBooking::create([
            'bike_id'           => $data['bike_id'],
            'comment'           => $data['comment'] ?? null,
            'customer_name'     => $data['customer_name'],
            'ends_at'           => Carbon::parse($data['ends_at'])->toDateString(),
            'phone'             => $data['phone'] ?? null,
            'starts_at'         => Carbon::parse($data['starts_at'])->toDateString(),
            'status'            => BookingStatusEnum::Booked->value,
            'telegram_username' => $data['telegram_username'] ?? null,
        ]);

        return back()->with('success', 'Бронирование создано');
    }
}