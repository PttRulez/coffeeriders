<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WorkshopSparePartPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
            'purchase_price_rub' => ['required', 'integer', 'min:0'],
            'purchased_at' => ['required', 'date'],
            'comment' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'quantity' => 'количество',
            'purchase_price_rub' => 'цена закупки',
            'purchased_at' => 'дата закупки',
            'comment' => 'комментарий',
        ];
    }
}
