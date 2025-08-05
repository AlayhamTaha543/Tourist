<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'registration_number' => $this->registration_number,
            'model' => $this->model,
            'year' => $this->year,
            'color' => $this->color,
            'is_active' => $this->is_active,

            // Relationships
            'vehicle_type' => $this->when($this->relationLoaded('vehicleType'), function () {
                return [
                    'id' => $this->vehicleType->id,
                    'name' => $this->vehicleType->name,
                    'max_passengers' => $this->vehicleType->max_passengers,
                    'image_url' => $this->vehicleType->image_url,
                    'price_info' => [
                        'base_price' => $this->vehicleType->base_price,
                        'price_per_km' => $this->vehicleType->price_per_km,
                    ],
                ];
            }),

            'taxi_service' => $this->when($this->relationLoaded('taxiService'), function () {
                return [
                    'id' => $this->taxiService->id,
                    'name' => $this->taxiService->name,
                ];
            }),

            // HATEOAS links
            'links' => [
                'self' => route('api.vehicles.show', $this->id),
            ],
        ];
    }
}
