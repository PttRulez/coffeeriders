<?php

namespace App\Http\Requests;

use App\Enums\BikeCategory;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateBikeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return request()->user()->isAdmin();
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'img' => 'sometimes',
            'images' => ['nullable','array'],
            'images.*' => ['image','max:8192'], // 8 МБ на файл
            'category' => ['required', new Enum(BikeCategory::class)],
            'prices' => ['required', 'array', function ($attribute, $value, $fail) {
                if (count($value) < 1) {
                    $fail('At least one price is required.');
                }
            }],
            'prices.*.price' => 'required',
            'prices.*.period' => 'required|string',
            'short_description' => 'required|string',
            'full_description' => 'sometimes',
            'is_published' => 'sometimes|boolean',
        ];
    }
}
