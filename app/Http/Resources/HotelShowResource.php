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
            'image' => $this->main_image ? asset('storage/' . $this->main_image) : null,
            'room_types' => $this->roomTypes->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'price' => $room->base_price,
                    'availability' => $room->availability
                        ->filter(function ($availability) {
                            return $availability->available_rooms > 0;
                        })
                        ->map(function ($availability) {
                            return [
                                'date' => $availability->date->format('Y-m-d'),
                                'available_rooms' => $availability->available_rooms,
                                'price' => $availability->price,
                            ];
                        })
                        ->values(), // Reset array keys after filtering
                ];
            }),
            'reviews' => FeedbackResource::collection($this->feedbacks ?? collect()),
        ];
    }
}
