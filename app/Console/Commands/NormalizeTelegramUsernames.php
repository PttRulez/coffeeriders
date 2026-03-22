<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\TelegramUsernameExtractor;
use Illuminate\Console\Command;

class NormalizeTelegramUsernames extends Command
{
    protected $signature = 'telegram:normalize-usernames';
    protected $description = 'Нормализует все Telegram usernames в базе данных';

    public function handle(): int
    {
        $users = User::whereNotNull('telegram_username')->get();
        
        $updated = 0;
        $failed = 0;
        $skipped = 0;
        
        $this->info("Найдено пользователей с Telegram: {$users->count()}");
        
        foreach ($users as $user) {
            $original = $user->telegram_username;

            $normalized = TelegramUsernameExtractor::extract($original);

            if ($normalized !== null) {
                
                if ($normalized === $original) {
                    $skipped++;
                    continue;
                }
                
                $user->telegram_username = $normalized;
                $user->save();
                
                $this->line("✓ User #{$user->id}: '{$original}' → '{$normalized}'");
                $updated++;
                
            } else {
                $this->error("✗ User #{$user->id}: '{$original}' - Некорректный формат Telegram username");
                $failed++;
            }
        }
        
        $this->newLine();
        $this->info("Обработка завершена:");
        $this->info("  Обновлено: {$updated}");
        $this->info("  Пропущено (уже нормализовано): {$skipped}");
        $this->info("  Ошибок: {$failed}");
        
        return self::SUCCESS;
    }
}
