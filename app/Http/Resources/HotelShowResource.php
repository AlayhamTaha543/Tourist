<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelShowResource extends JsonResource
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
            'description' => $this->description,
            'location' => $this->location ? $this->location->fullName() : null,
            'average_rating' => $this->average_rating,
            'image' => $this->main_image,
            'room_types' => $this->roomTypes->map(function ($room) {
                return [
                    'name' => $room->name,
                    'price' => $room->base_price,
                    'number' => $room->number,
                ];
            }),
        ];
    }
}
