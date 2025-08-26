<?php

// RentalOfficeResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalOfficeCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultImage = "images/rental/r.png";

        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location->fullName(),
            'description' => $this->address,
            'open_time' => $this->open_time ? $this->open_time->format('H:i') : null,
            'close_time' => $this->close_time ? $this->close_time->format('H:i') : null,
            'is_closed' => $this->is_closed,
            'image' => $this->image ? asset('storage/' . $this->image) : asset('storage/' . $defaultImage),
            'rating' => (float) $this->rating,
        ];
    }
}
