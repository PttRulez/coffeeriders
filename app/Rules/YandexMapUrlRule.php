<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class YandexMapUrlRule implements ValidationRule
{
    public function validate(string $attribute, mixed $urlToCheck, Closure $fail): void
    {
        if (!is_string($urlToCheck) || trim($urlToCheck) === '') {
            return;
        }

        $urlToCheck = trim($urlToCheck);
        $host = parse_url($urlToCheck, PHP_URL_HOST);
        $path = parse_url($urlToCheck, PHP_URL_PATH);
        
        $isYandexHost = is_string($host) && in_array(strtolower($host), ['yandex.ru', 'www.yandex.ru'], true);
        $isMapsPath = is_string($path) && str_starts_with($path, '/maps');

        if (!$isYandexHost || !$isMapsPath) {
            $fail('Укажите ссылку Яндекс Карт или координаты в формате "широта, долгота".');
        }
    }
}
