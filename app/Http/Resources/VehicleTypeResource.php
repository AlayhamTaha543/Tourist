<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleTypeResource extends JsonResource
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
            'taxi_service_id' => $this->taxi_service_id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image_url ? asset('storage/' . $this->main_image) : null,
            'max_passengers' => $this->max_passengers,
            'price_per_km' => (float) $this->price_per_km,
            'base_price' => (float) $this->base_price,
            'image_url' => $this->image_url,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}