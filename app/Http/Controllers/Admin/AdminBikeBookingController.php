<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCreateBikeBookingRequest;
use App\Http\Resources\BikeResource;
use App\Models\Bike;
use App\Models\BikeBooking;
use App\Support\TelegramUsernameExtractor;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminBikeBookingController extends Controller
{
    public function index(): Response
    {
        $showArchive = request()->boolean('archive');

        return Inertia::render('adminka/rent-bikes/Bookings', [
            'bookings' => BikeBooking::with('bike')
                ->when(! $showArchive, fn ($query) => $query->currentOrUpcoming())
                ->chronological()
                ->get(),
            'archive' => $showArchive,
        ]);
    }

    public function create(Request $request): Response
    {
        $bike = Bike::query()->findOrFail((int) $request->integer('bike'));
        $bikeData = BikeResource::make($bike)->resolve();
        $bikeData['predoplata'] = 0;

        return Inertia::render('adminka/rent-bikes/CreateBooking', [
            'bike' => $bikeData,
        ]);
    }

    public function store(AdminCreateBikeBookingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = Carbon::parse($data['ends_at']);

        BikeBooking::create([
            'bike_id' => $data['bike_id'],
            'comment' => $data['comment'] ?? null,
            'customer_name' => $data['customer_name'],
            'telegram_username' => $this->normalizeTelegramUsername($data['telegram_username'] ?? null),
            'phone' => $data['phone'] ?? null,
            'starts_at' => $startsAt->toDateString(),
            'ends_at' => $endsAt->toDateString(),
            'days_count' => $startsAt->diffInDays($endsAt) + 1,
            'status' => BookingStatusEnum::Booked->value,
            'paid_money' => 0,
        ]);

        return redirect()
            ->route('adminka.rent-bikes.bookings.index')
            ->with('success', 'Бронь создана без оплаты');
    }

    public function destroy(BikeBooking $bikeBooking): RedirectResponse
    {
        $bikeBooking->delete();

        return back()->with('success', 'Бронь удалена');
    }

    private function normalizeTelegramUsername(?string $value): ?string
    {
        return TelegramUsernameExtractor::extract($value);
    }
}
