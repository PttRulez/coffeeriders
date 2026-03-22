<?php

namespace App\Rules;

use App\Support\TelegramUsernameExtractor;
use Illuminate\Contracts\Validation\Rule;

class TelegramUsername implements Rule
{
    private ?string $extractedUsername = null;
    
    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return true; // Пустое значение обрабатывается через nullable
        }

        $this->extractedUsername = TelegramUsernameExtractor::extract($value);

        return $this->extractedUsername !== null;
    }
    
    public function message(): string
    {
        return 'Некорректный формат Telegram username. Используйте: @nickname, nickname, t.me/nickname или https://t.me/nickname';
    }
    
    public function getExtractedUsername(): ?string
    {
        return $this->extractedUsername;
    }
}
