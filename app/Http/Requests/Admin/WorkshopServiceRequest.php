<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WorkshopServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'workshop_category_id' => ['required', 'integer', 'exists:workshop_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'price_rub' => ['required', 'integer', 'min:0'],
            'additional_info' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'workshop_category_id' => 'категория',
            'name' => 'название',
            'price_rub' => 'цена',
            'additional_info' => 'дополнительная информация',
        ];
    }
}
