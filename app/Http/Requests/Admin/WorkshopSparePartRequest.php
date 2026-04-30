<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WorkshopSparePartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'workshop_spare_part_category_id' => [
                'required',
                'integer',
                'exists:workshop_spare_part_categories,id',
            ],
            'name' => ['required', 'string', 'max:150'],
            'comment' => ['nullable', 'string', 'max:500'],
            'sale_price_rub' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:8192'],
        ];
    }

    public function attributes(): array
    {
        return [
            'workshop_spare_part_category_id' => 'категория',
            'name' => 'название',
            'comment' => 'комментарий',
            'sale_price_rub' => 'цена продажи',
            'photo' => 'фото',
        ];
    }
}
