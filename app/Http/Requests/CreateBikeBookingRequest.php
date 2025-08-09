<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBikeBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bike_id'           => ['required','integer','exists:bikes,id'],
            'customer_name'     => ['required','string','max:120'],

            // хотя бы одно из двух:
            'telegram_username' => ['nullable','string','max:64','required_without:phone'],
            'phone'             => ['nullable','string','max:32','required_without:telegram_username'],

            'comment'           => ['nullable','string','max:2000'],
            'starts_at'         => ['required','date'],
            'ends_at'           => ['required','date','after_or_equal:starts_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required'      => 'Укажите имя.',
            'bike_id.exists'              => 'Велосипед не найден.',
            'telegram_username.required_without' => 'Укажите Telegram или телефон.',
            'phone.required_without'      => 'Укажите телефон или Telegram.',
            'ends_at.after_or_equal'      => 'Дата окончания не может быть раньше даты начала.',
        ];
    }

}