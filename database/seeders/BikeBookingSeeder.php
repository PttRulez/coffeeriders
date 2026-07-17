<?php

namespace Database\Seeders;

use App\Enums\BookingStatusEnum;
use App\Models\Bike;
use App\Models\BikeBooking;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BikeBookingSeeder extends Seeder
{
    public function run(): void
    {
        if (Bike::query()->count() < 6) {
            $this->call(BikeSeeder::class);
        }

        $bikes = Bike::query()->orderBy('id')->get();

        if ($bikes->isEmpty()) {
            return;
        }

        $today = today();
        $windowStart = $today->copy()->subDays(30);
        $windowEnd = $today->copy()->addDays(30);
        $created = 0;
        $target = 100;

        foreach ($bikes as $bikeIndex => $bike) {
            $startsAt = $windowStart->copy()->addDays($bikeIndex % 3);

            while ($startsAt->lte($windowEnd) && $created < $target) {
                $durationDays = ($created % 5) + 1;
                $endsAt = $startsAt->copy()->addDays($durationDays - 1);

                if ($endsAt->gt($windowEnd)) {
                    break;
                }

                BikeBooking::query()->create([
                    'bike_id' => $bike->id,
                    'customer_name' => 'Тестовый клиент '.($created + 1),
                    'telegram_username' => 'test_booking_'.($created + 1),
                    'phone' => '+7999000'.str_pad((string) ($created + 1), 4, '0', STR_PAD_LEFT),
                    'comment' => 'Тестовая бронь из сидера',
                    'starts_at' => $startsAt->toDateString(),
                    'ends_at' => $endsAt->toDateString(),
                    'days_count' => $durationDays,
                    'status' => BookingStatusEnum::Booked->value,
                    'paid_money' => ($created % 4 === 0) ? 1500 : 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $created++;
                $startsAt->addDays($durationDays);
            }
        }
    }
}
