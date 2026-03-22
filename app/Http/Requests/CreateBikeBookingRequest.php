<?php

namespace App\Http\Requests;

use App\Models\BikeBooking;
use App\Support\TelegramUsernameExtractor;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CreateBikeBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'bike_id' => ['required', 'integer', 'exists:bikes,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            
            // хотя бы одно из двух:
            'telegram_username' => ['nullable', 'string', 'max:64', 'required_without:phone'],
            'phone' => ['nullable', 'string', 'max:32', 'required_without:telegram_username'],
            
            'comment' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Укажите имя.',
            'bike_id.exists' => 'Велосипед не найден.',
            'telegram_username.required_without' => 'Укажите Telegram или телефон.',
            'phone.required_without' => 'Укажите телефон или Telegram.',
            'ends_at.after_or_equal' => 'Дата окончания не может быть раньше даты начала.',
        ];
    }
    
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return; // уже есть ошибки — дальше не проверяем
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
            
            // 1. Проверка на прошлое
            if ($start->isBefore(today())) {
                $v->errors()->add('starts_at', 'Дата начала не может быть в прошлом.');
                return; // дальше уже нет смысла проверять
            }
            
            $bikeId = (int)$this->input('bike_id');
            $end = Carbon::parse($this->input('ends_at'))->toDateString();
            $start = $start->toDateString();
            
            // 2. Проверка пересечений: [start, end) против существующих [s, e)
            $overlaps = BikeBooking::query()
                ->where('bike_id', $bikeId)
                ->where('starts_at', '<', $end)   // existing.start < new.end
                ->where('ends_at', '>', $start) // existing.end   > new.start
                ->exists();
            
            if ($overlaps) {
                $v->errors()->add('starts_at', 'Выбранный диапазон дат уже занят.');
            }
        });
    }
    
}
