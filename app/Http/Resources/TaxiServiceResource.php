<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxiServiceResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'logo_url' => $this->logo_url,
            'website' => $this->website,
            'phone' => $this->phone,
            'email' => $this->email,
            'average_rating' => (float) $this->average_rating,
            'total_ratings' => (int) $this->total_ratings,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            // Include location data if relationship is loaded
            'location' => $this->when($this->relationLoaded('location'), function () {
                return [
                    'id' => $this->location->id,
                    'address' => $this->location->address,
                    'city' => $this->location->city->name,
                    'country' => $this->location->city->country->name,
                ];
            }),
            // Include vehicles if relationship is loaded
            'vehicles' => $this->when($this->relationLoaded('vehicles'), function () {
                return VehicleResource::collection($this->vehicles);
            }),
            // Include drivers if relationship is loaded
            'drivers' => $this->when($this->relationLoaded('drivers'), function () {
                return DriverResource::collection($this->drivers);
            }),
        ];
    }
}
