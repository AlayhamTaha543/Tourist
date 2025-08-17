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
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'vehicle_type' => $this->vehicleType->name,



            // HATEOAS links
            'links' => [
                'self' => route('api.vehicles.show', $this->id),
            ],
        ];
    }
}