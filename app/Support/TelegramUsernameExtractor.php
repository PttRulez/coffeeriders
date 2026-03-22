<?php

namespace App\Support;

class TelegramUsernameExtractor
{
    public static function extract(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $input = trim($value);

        if ($input === '') {
            return null;
        }

        $patterns = [
            '/^@([a-zA-Z0-9_]{5,32})$/',
            '/^([a-zA-Z0-9_]{5,32})$/',
            '/^t\.me\/([a-zA-Z0-9_]{5,32})$/',
            '/^https?:\/\/t\.me\/([a-zA-Z0-9_]{5,32})$/',
            '/^https?:\/\/t\.me\/s\/([a-zA-Z0-9_]{5,32})$/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
