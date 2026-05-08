<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileUserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'telegram_username' => $this->telegram_username,
            'height' => $this->height,
            'weight' => $this->weight,
            'pedals' => $this->pedals,
            'paid_cycling_count' => $this->paid_cycling_count,
            'avatar' => $this->avatar,
        ];
    }
}
