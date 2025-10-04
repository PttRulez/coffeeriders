<?php

namespace App\Services\AdminTelegram;

use App\Models\BikeBooking;
use App\Models\CyclingActivity;
use App\Services\AdminTelegram\Dto\FeedBackFormDto;
use App\Services\FeedBackDto;
use Carbon\Carbon;
use Telegram\Bot\Api;
use function route;

class AdminTelegram
{
    protected Api $adminBot;
    protected Api $prokatBot;
    protected Api $studioBot;
    
    public function __construct()
    {
        $this->prokatBot = new Api(config('telegram.bots.prokat.token'));
        $this->studioBot = new Api(config('telegram.bots.studio.token'));
        $this->adminBot = new Api(config('telegram.bots.admin.token'));
    }
    
    public function sendMessage(string $text, ?int $chatId = null): void
    {
        $this->api->sendMessage([
            'chat_id' => $chatId ?? config('telegram.admin_chat_id'),
            'text' => $text,
        ]);
    }
    
    public function sendProkatBookingNotification(BikeBooking $booking): void
    {
        $telegram = $booking->telegram_username
            ? '@' . ltrim($booking->telegram_username, '@')
            : '—';
        
        $bikeUrl = route('rent-bikes.show', $booking->bike->id);
        
        $startsAt = Carbon::parse($booking->starts_at)->translatedFormat('d F');
        $endsAt = Carbon::parse($booking->ends_at)->translatedFormat('d F');
        
        $text = "🟢 Новое бронирование (Прокат)\n\n"
            . "🚲 Велосипед: <a href=\"{$bikeUrl}\">{$booking->bike->name}</a>\n"
            . "👤 {$booking->customer_name}\n"
            . "📅 {$startsAt} – {$endsAt}\n"
            . "📞 {$booking->phone}\n"
            . "📱 Telegram: {$telegram}\n"
            . "💬 Коммент: {$booking->comment}";
        
        $this->prokatBot->sendMessage([
            'chat_id' => config('telegram.admin_chat_id'),
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }
    
    public function sendStudioBookingNotification(CyclingActivity $cyclingActivity): void
    {
        $cyclingActivity->loadMissing('user');
        
        $telegram = $cyclingActivity->user->telegram_username
            ? '@' . ltrim($cyclingActivity->user->telegram_username, '@')
            : '—';
        
        $startsAt = Carbon::parse($cyclingActivity->starts_at)->translatedFormat('d F H:i');
        
        $text = "🟢 Новое бронирование (Студия)\n\n"
            . "👤 {$cyclingActivity->user->name}\n"
            . "📅 {$startsAt}\n"
            . "📞 {$cyclingActivity->user->phone}\n"
            . "📱 Telegram: {$telegram}\n";
        
        $this->studioBot->sendMessage([
            'chat_id' => config('telegram.admin_chat_id'),
            'text' => $text,
        ]);
    }
    
    public function sendFeedbackFormNotification(FeedBackFormDto $dto): void
    {
        $text = "🟢 Новое сообщение (Форма обратной связи)\n\n"
            . "👤 Имя: {$dto->name}\n"
            . "📞 Телефон: {$dto->phone}\n"
            . "✉️ Сообщение:\n{$dto->message}\n";
        
        $this->adminBot->sendMessage([
            'chat_id' => config('telegram.admin_chat_id'),
            'text' => $text,
        ]);
    }
    
}