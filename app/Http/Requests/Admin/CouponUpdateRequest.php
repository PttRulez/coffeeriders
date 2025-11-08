<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() ?? false;
    }
    
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons')
                ->where('service_type', $this->service_type)
                ->ignore($this->coupon?->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', 'in:percent,fixed'],
            'discount_value' => ['required', 'integer', 'min:1', 'max:100000'],
            'service_type' => ['required', 'in:cycling,bike_rent'],
            'is_active' => ['required', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'max_uses_per_user' => ['nullable', 'integer', 'min:1'],
        ];
    }
    
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->code ? mb_strtoupper(trim($this->code)) : null,
            'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);
    }
    
    public function messages(): array
    {
        return [
            'code.unique' => 'купон с таким именем и видом услуги уже есть'
        ];
    }
}