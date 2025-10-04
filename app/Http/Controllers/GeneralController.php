<?php

namespace App\Http\Controllers;

use App\Services\AdminTelegram\AdminTelegram;
use App\Services\AdminTelegram\Dto\FeedBackFormDto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeneralController extends Controller
{
    public function feedBackForm(Request $request, AdminTelegram $telegram): RedirectResponse
    {
        $telegram->sendFeedbackFormNotification(
            new FeedBackFormDto(
                name: $request->name,
                message: $request->message,
                phone: $request->phone,
            )
        );
        
        return back()->with('success', 'Спасибо за ваше обращение');
    }
}