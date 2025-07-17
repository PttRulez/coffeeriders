<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Markdown;

class BikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category' => $this->category,
            'img_url' => asset($this->img_url),
            'name' => $this->name,
            'prices' => $this->prices,
            'short_description' => $this->short_description,
            'full_description' => $this->full_description,
        ];
    }
}
