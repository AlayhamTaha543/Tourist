<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HotelCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'hotels' => $this->collection->map(function ($hotel) {
                    return [
                        'name' => $hotel->name,
                        'location' => $hotel->location ? $hotel->location->fullName() : null,
                        'price' => $this->getLowestRoomPrice($hotel),
                        'image' => $hotel->main_image,
                        'rating' => $hotel->average_rating,
                        'recommended' => $hotel->is_recommended,
                        'popular' => $hotel->is_popular,
                    ];
                }),
            ],
            'message' => 'All hotels retrieved successfully'
        ];
    }

    /**
     * Get the lowest price from hotel's room types
     *
     * @param mixed $hotel
     * @return float|null
     */
    private function getLowestRoomPrice($hotel): ?float
    {
        if ($hotel->roomTypes && $hotel->roomTypes->isNotEmpty()) {
            return $hotel->roomTypes->min('base_price');
        }

        return null;
    }
}
