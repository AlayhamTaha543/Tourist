<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
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
            'duration_hours' => $this->duration_hours,
            'duration_days' => $this->duration_days,
            'base_price' => $this->base_price,
            'average_rating' => $this->average_rating,
            'main_image' => $this->main_image ? asset('storage/' . $this->main_image) : null,
            'reviews' => FeedbackResource::collection($this->whenLoaded('feedbacks')),
        ];
    }
}
