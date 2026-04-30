<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BikeBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer_name,
            'phone' => $this->phone,
            'telegram_username' => $this->telegram_username,
            'comment' => $this->comment,
            'paid_money' => (int) $this->paid_money,
            'ends_at' => $this->ends_at->format('Y-m-d'),
            'starts_at' => $this->starts_at->format('Y-m-d'),
        ];
    }
    
}
