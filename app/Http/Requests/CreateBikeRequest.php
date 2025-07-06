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
            'category' => ['required', new Enum(BikeCategory::class)],
            'prices.*.price' => 'required',
            'prices.*.period' => 'required|string',
            'short_description' => 'required|string',
            'full_description' => 'sometimes',
        ];
    }
}