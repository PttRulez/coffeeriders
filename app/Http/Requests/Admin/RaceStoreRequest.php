<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RaceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'price' => 'required|integer|min:0',
            'is_published' => 'boolean',
            'clusters' => 'required|array|min:1',
            'clusters.*.id' => 'nullable|integer',
            'clusters.*.name' => 'required|string|max:255',
            'clusters.*.start_time' => 'required|date_format:H:i',
            'clusters.*.duration_minutes' => 'required|integer|min:1',
            'clusters.*.price' => 'nullable|integer|min:0',
        ];
    }
}
