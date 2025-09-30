<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPasswordBase
{
    /**
     * Построить письмо.
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Сброс пароля на Coffee Riders 🚴')
            ->greeting('Привет, ' . $notifiable->name . '!')
            ->line('Ты запросил(а) сброс пароля. Чтобы задать новый пароль, перейди по ссылке ниже:')
            ->action('Сбросить пароль', $url)
            ->line('Если ты не запрашивал(а) смену пароля, просто проигнорируй это письмо.')
            ->salutation('С уважением, команда Coffee Riders');
    }
}