<?php

namespace App\Http\Requests\Admin;

use App\Models\BikeBooking;
use App\Support\TelegramUsernameExtractor;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AdminCreateBikeBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'bike_id' => ['required', 'integer', 'exists:bikes,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'telegram_username' => ['nullable', 'string', 'max:64'],
            'phone' => ['nullable', 'string', 'max:32'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $telegram = $this->input('telegram_username');

            if ($telegram) {
                $normalizedTelegram = TelegramUsernameExtractor::extract($telegram);

                if ($normalizedTelegram === null) {
                    $v->errors()->add(
                        'telegram_username',
                        'Некорректный формат Telegram username. Используйте: @nickname, nickname, t.me/nickname или https://t.me/nickname'
                    );
                    return;
                }

                $this->merge(['telegram_username' => $normalizedTelegram]);
            }

            $start = Carbon::parse($this->input('starts_at'));

            if ($start->isBefore(today())) {
                $v->errors()->add('starts_at', 'Дата начала не может быть в прошлом.');
                return;
            }

            $bikeId = (int) $this->input('bike_id');
            $end = Carbon::parse($this->input('ends_at'))->toDateString();
            $start = $start->toDateString();

            $overlaps = BikeBooking::query()
                ->where('bike_id', $bikeId)
                ->where('starts_at', '<', $end)
                ->where('ends_at', '>', $start)
                ->exists();

            if ($overlaps) {
                $v->errors()->add('starts_at', 'Выбранный диапазон дат уже занят.');
            }
        });
    }
}
