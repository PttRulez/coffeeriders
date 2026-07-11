<?php

namespace Tests\Feature;

use App\Enums\BikeCategory;
use App\Enums\BookingStatusEnum;
use App\Enums\Role;
use App\Models\Bike;
use App\Models\BikeBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AdminBikeBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_booking_list_hides_bookings_ended_before_today(): void
    {
        Carbon::setTestNow('2026-07-09 12:00:00');
        $this->beforeApplicationDestroyed(fn () => Carbon::setTestNow());

        $admin = User::factory()->create(['role' => Role::ADMIN->value]);
        $bike = $this->createBike();

        $this->createBooking($bike, '2026-07-01', '2026-07-08');
        $endingTodayBooking = $this->createBooking($bike, '2026-07-08', '2026-07-09');
        $futureBooking = $this->createBooking($bike, '2026-07-10', '2026-07-12');

        $this->actingAs($admin)
            ->get(route('adminka.rent-bikes.bookings.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('adminka/rent-bikes/Bookings')
                ->has('bookings', 2)
                ->where('archive', false)
                ->where('bookings', fn ($bookings) => $bookings->pluck('id')->all() === [
                    $endingTodayBooking->id,
                    $futureBooking->id,
                ])
            );
    }

    public function test_admin_booking_list_shows_all_bookings_in_archive_mode(): void
    {
        Carbon::setTestNow('2026-07-09 12:00:00');
        $this->beforeApplicationDestroyed(fn () => Carbon::setTestNow());

        $admin = User::factory()->create(['role' => Role::ADMIN->value]);
        $bike = $this->createBike();

        $pastBooking = $this->createBooking($bike, '2026-07-01', '2026-07-08');
        $endingTodayBooking = $this->createBooking($bike, '2026-07-08', '2026-07-09');
        $futureBooking = $this->createBooking($bike, '2026-07-10', '2026-07-12');

        $this->actingAs($admin)
            ->get(route('adminka.rent-bikes.bookings.index', ['archive' => 1]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('adminka/rent-bikes/Bookings')
                ->has('bookings', 3)
                ->where('archive', true)
                ->where('bookings', fn ($bookings) => $bookings->pluck('id')->all() === [
                    $pastBooking->id,
                    $endingTodayBooking->id,
                    $futureBooking->id,
                ])
            );
    }

    public function test_admin_bike_page_shows_bookings_chronologically(): void
    {
        $admin = User::factory()->create(['role' => Role::ADMIN->value]);
        $bike = $this->createBike();

        $futureBooking = $this->createBooking($bike, '2026-07-10', '2026-07-12');
        $pastBooking = $this->createBooking($bike, '2026-07-01', '2026-07-08');
        $middleBooking = $this->createBooking($bike, '2026-07-08', '2026-07-09');

        $this->actingAs($admin)
            ->get(route('adminka.rent-bikes.show', ['bike' => $bike]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('adminka/rent-bikes/Show')
                ->where('bike.bookings', fn ($bookings) => $bookings->pluck('id')->all() === [
                    $pastBooking->id,
                    $middleBooking->id,
                    $futureBooking->id,
                ])
            );
    }

    private function createBike(): Bike
    {
        return Bike::query()->create([
            'category' => BikeCategory::Road->value,
            'name' => 'Test Bike',
            'prices' => [['period' => '1 день', 'price' => 1000]],
            'short_description' => 'Test bike',
            'full_description' => null,
        ]);
    }

    private function createBooking(Bike $bike, string $startsAt, string $endsAt): BikeBooking
    {
        return BikeBooking::query()->create([
            'bike_id' => $bike->id,
            'customer_name' => 'Customer',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => BookingStatusEnum::Booked->value,
        ]);
    }
}
