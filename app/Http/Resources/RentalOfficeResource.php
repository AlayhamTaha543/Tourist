<?php

// RentalOfficeResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalOfficeResource extends JsonResource
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
            'location' => $this->location->fullName(),
            'image' => $this->image,
            'rating' => (float) $this->rating,

            // Include vehicles if relationship is loaded
            'vehicles' => $this->when($this->relationLoaded('vehicles'), function () {
                return $this->vehicles->map(function ($vehicle) {
                    return [
                        'license_plate' => $vehicle->license_plate,
                        'make' => $vehicle->make,
                        'model' => $vehicle->model,
                        'year' => $vehicle->year,
                        'seating_capacity' => $vehicle->seating_capacity,
                        'price_per_day' => $vehicle->price_per_day,
                        'status' => $vehicle->status,
                        'category' => $this->when($vehicle->relationLoaded('category'), [
                            'name' => $vehicle->category->name,
                            'description' => $vehicle->category->description,
                        ]),
                    ];
                });
            }),

            // Include vehicle categories if relationship is loaded
            'vehicle_categories' => $this->when($this->relationLoaded('vehicleCategories'), function () {
                return $this->vehicleCategories->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'description' => $category->description,
                        'vehicles_count' => $category->vehicles->count(),
                    ];
                });
            }),
        ];
    }
}
