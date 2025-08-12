<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'location' => $this->location ? $this->location->fullName() : null,
            'price' => $this->getLowestRoomPrice(),
            'image' => $this->main_image,
            'rating' => $this->average_rating,
        ];
    }

    /**
     * Get the lowest price from hotel's room types
     *
     * @return float|null
     */
    private function getLowestRoomPrice(): ?float
    {
        if ($this->roomTypes && $this->roomTypes->isNotEmpty()) {
            return $this->roomTypes->min('base_price');
        }

        return null;
    }
}
