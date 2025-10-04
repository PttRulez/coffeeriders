<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/telegram-auth', function (Request $request) {
    $initDataRaw = $request->input('initData');
    if (!verifyTelegramInitData($initDataRaw)) {
        return response()->json(['error' => 'Invalid Telegram signature'], 401);
    }

    // Пример: извлекаем ID пользователя
    parse_str($initDataRaw, $parsed);
    $userId = $parsed['user']['id'] ?? null;

    return response()->json(['status' => 'ok', 'user_id' => $userId]);
});

require __DIR__ . '/webhooks.php';