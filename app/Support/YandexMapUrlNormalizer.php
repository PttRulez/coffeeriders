<?php

namespace App\Support;

class YandexMapUrlNormalizer
{
    public static function normalize(?string $raw): ?string
    {
        $raw = trim((string) $raw);

        if ($raw === '') {
            return null;
        }
        
        // если прислали координаты, то конвертнет и отдаст ссылку на Яндекс.Карты
        if (preg_match('/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/', $raw, $m)) {
            $lat = (float) $m[1];
            $lon = (float) $m[2];

            if ($lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180) {
                return self::buildUrl($lat, $lon);
            }
        }

        return $raw;
    }

    private static function buildUrl(float $lat, float $lon): string
    {
        $latS = rtrim(rtrim(sprintf('%.6f', $lat), '0'), '.');
        $lonS = rtrim(rtrim(sprintf('%.6f', $lon), '0'), '.');

        return "https://yandex.ru/maps/?ll={$lonS},{$latS}&z=16&pt={$lonS},{$latS},pm2rdm";
    }
}

