<?php

function verifyTelegramInitData(string $initDataRaw): bool
{
    $botToken = env('TELEGRAM_BOT_TOKEN');
    parse_str($initDataRaw, $initData);

    $hash = $initData['hash'] ?? '';
    unset($initData['hash']);

    ksort($initData);
    $dataCheckString = collect($initData)
        ->map(fn($v, $k) => "$k=$v")
        ->implode("\n");

    $secretKey = hash('sha256', $botToken, true);
    $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

    return hash_equals($calculatedHash, $hash);
}
