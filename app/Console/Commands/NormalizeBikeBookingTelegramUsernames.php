<?php

namespace App\Console\Commands;

use App\Models\BikeBooking;
use App\Support\TelegramUsernameExtractor;
use Illuminate\Console\Command;

class NormalizeBikeBookingTelegramUsernames extends Command
{
    protected $signature = 'telegram:normalize-bike-booking-usernames';
    protected $description = 'Нормализует Telegram usernames в бронированиях велосипедов';

    public function handle(): int
    {
        $total = BikeBooking::query()->whereNotNull('telegram_username')->count();
        $updated = 0;
        $failed = 0;
        $skipped = 0;

        $this->info("Найдено бронирований с Telegram: {$total}");

        BikeBooking::query()
            ->whereNotNull('telegram_username')
            ->orderBy('id')
            ->chunkById(200, function ($bookings) use (&$updated, &$failed, &$skipped): void {
                foreach ($bookings as $booking) {
                    $original = $booking->telegram_username;
                    $normalized = TelegramUsernameExtractor::extract($original);

                    if ($normalized === null) {
                        $this->error(
                            "✗ Booking #{$booking->id}: '{$original}' - Некорректный формат Telegram username"
                        );
                        $failed++;
                        continue;
                    }

                    if ($normalized === $original) {
                        $skipped++;
                        continue;
                    }

                    $booking->telegram_username = $normalized;
                    $booking->save();

                    $this->line("✓ Booking #{$booking->id}: '{$original}' → '{$normalized}'");
                    $updated++;
                }
            });

        $this->newLine();
        $this->info('Обработка завершена:');
        $this->info("  Обновлено: {$updated}");
        $this->info("  Пропущено (уже нормализовано): {$skipped}");
        $this->info("  Ошибок: {$failed}");

        return self::SUCCESS;
    }
}
