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
        $defaultImage = "images/rental/r.png";
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location->fullName(),
            'open_time' => $this->open_time ? $this->open_time->format('H:i') : null,
            'close_time' => $this->close_time ? $this->close_time->format('H:i') : null,
            'is_closed' => $this->is_closed,
            'logo_url' => $this->image ? asset('storage/' . $this->image) : asset('storage/' . $defaultImage),
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
                        'vehicle_type' => $vehicle->category->name,
                        'vehicle_type_id' => $vehicle->category->id,
                        'image' => $vehicle->image ? asset('storage/' . $vehicle->image) : asset('storage/' . "images/rental/r.png"),

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
            'reviews' => FeedbackResource::collection($this->feedbacks ?? collect()),

        ];
    }
}
