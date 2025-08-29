<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalVehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'price_per_day' => $this->price_per_day,
            'license_plate' => $this->license_plate,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'image' => $this->image,
            'seating_capacity' => $this->seating_capacity,
            'status' => $this->status,
            'vehicle_type' => $this->category->name,
            'vehicle_type_id' => $this->category->id,


        ];
    }
}
