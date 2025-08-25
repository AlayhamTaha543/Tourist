<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxiServiceCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultImage = "images/taxi/t.png";

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,

            'logo_url' => $this->logo_url ? asset('storage/' . $this->logo_url) : asset('storage/' . $defaultImage),
            'website' => $this->website,
            // 'phone' => $this->phone,
            // 'email' => $this->email,
            'rating' => (float) $this->average_rating,
            'is_active' => (bool) $this->is_active,
            'location' => $this->location->fullName(),
            // 'total_ratings' => (int) $this->total_ratings,
            // 'created_at' => $this->created_at?->toIso8601String(),
            // 'updated_at' => $this->updated_at?->toIso8601String(),
            // Include location data if relationship is loaded
            // // Include vehicles if relationship is loaded
            // 'vehicles' => $this->when($this->relationLoaded('vehicles'), function () {
            //     return VehicleResource::collection($this->vehicles);
            // }),
            // // Include drivers if relationship is loaded
            // 'drivers' => $this->when($this->relationLoaded('drivers'), function () {
            //     return DriverResource::collection($this->drivers);
            // }),
        ];
    }
}