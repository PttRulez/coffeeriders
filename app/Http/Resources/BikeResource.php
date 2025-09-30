<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $bookedDates = [];
        
        foreach ($this->bookings as $booking) {
            $start = Carbon::parse($booking->starts_at);
            $end = Carbon::parse($booking->ends_at);
            
            // один день аренды
            if ($start->equalTo($end)) {
                $bookedDates[] = $start->toDateString();
                continue;
            }
            
            // иначе блокируем все дни до даты возврата (не включая её)
            while ($start->lt($end)) {
                $bookedDates[] = $start->toDateString();
                $start->addDay();
            }
        }
        
        $oneDayPrice = min(array_column($this->prices, 'price'));
        
        return [
            'id' => $this->id,
            'bookings' => BikeBookingResource::collection($this->bookings)->resolve(),
            'booked_dates' => $bookedDates,
            'category' => $this->category,
            'images' => $this->images,
            'name' => $this->name,
            'prices' => $this->prices,
            'predoplata' => $oneDayPrice / 2,
            'short_description' => $this->short_description,
            'full_description' => $this->full_description,
        ];
    }
}
