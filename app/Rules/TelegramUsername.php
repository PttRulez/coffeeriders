<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TelegramUsername implements Rule
{
    private ?string $extractedUsername = null;
    
    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return true; // Пустое значение обрабатывается через nullable
        }
        
        $input = trim($value);
        
        // Паттерны для разных форматов
        $patterns = [
            '/^@([a-zA-Z0-9_]{5,32})$/',                           // @nickname
            '/^([a-zA-Z0-9_]{5,32})$/',                            // nickname
            '/^t\.me\/([a-zA-Z0-9_]{5,32})$/',                     // t.me/nickname
            '/^https?:\/\/t\.me\/([a-zA-Z0-9_]{5,32})$/',          // https://t.me/nickname
            '/^https?:\/\/t\.me\/s\/([a-zA-Z0-9_]{5,32})$/',       // https://t.me/s/nickname
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                $this->extractedUsername = $matches[1];
                return true;
            }
        }
        
        return false;
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