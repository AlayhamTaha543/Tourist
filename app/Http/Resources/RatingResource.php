<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'booking_id' => $this->booking_id,
            'rating_type' => $this->rating_type,
            'entity_id' => $this->entity_id,
            'rating' => (float) $this->rating,
            'comment' => $this->comment,
            'rating_date' => $this->rating_date?->toIso8601String(),
            'is_visible' => (bool) $this->is_visible,
            'admin_response' => $this->admin_response,
            // Include user data if relationship is loaded
            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            // Include entity data based on rating type if relationship is loaded
            'entity' => $this->when($this->relationLoaded('entity'), function () {
                switch ($this->rating_type) {
                    case 'driver':
                        return new DriverResource($this->entity);
                    case 'taxi_service':
                        return new TaxiServiceResource($this->entity);
                    default:
                        return null;
                }
            }),
        ];
    }
}
