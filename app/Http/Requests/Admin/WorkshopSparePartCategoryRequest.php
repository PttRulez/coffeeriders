<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkshopSparePartCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $categoryRouteParam = $this->route('workshop_spare_part_category');
        $categoryId = is_object($categoryRouteParam) ? $categoryRouteParam->id : $categoryRouteParam;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('workshop_spare_part_categories', 'name')->ignore($categoryId),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'название категории',
        ];
    }
}
