<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('bike_bookings', 'days_count')) {
            Schema::table('bike_bookings', function (Blueprint $table) {
                $table->unsignedInteger('days_count')->default(1)->after('ends_at');
            });
        }

        DB::table('bike_bookings')
            ->select(['id', 'starts_at', 'ends_at'])
            ->orderBy('id')
            ->chunkById(200, function ($bookings): void {
                foreach ($bookings as $booking) {
                    DB::table('bike_bookings')
                        ->where('id', $booking->id)
                        ->update([
                            'days_count' => max(
                                1,
                                Carbon::parse($booking->starts_at)
                                    ->diffInDays(Carbon::parse($booking->ends_at)),
                            ),
                        ]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('bike_bookings', 'days_count')) {
            return;
        }

        Schema::table('bike_bookings', function (Blueprint $table) {
            $table->dropColumn('days_count');
        });
    }
};
