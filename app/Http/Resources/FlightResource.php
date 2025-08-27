<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FlightTypeResource;

class FlightResource extends JsonResource
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
            'flight_number' => $this->flight_number,
            'departure' => $this->departure->name . ', ' .
                ($this->departure->city->name ?? '') . ', ' .
                ($this->departure->city->country->name ?? ''),
            'arrival' => $this->arrival->name . ', ' .
                ($this->arrival->city->name ?? '') . ', ' .
                ($this->arrival->city->country->name ?? ''),
            'departure_time' => $this->departure_time,
            'arrival_time' => $this->arrival_time,
            'price' => $this->price,
            'available_seats' => $this->available_seats,
            'rating' => round($this->rating ?? 0, 1),
            'flight_types' => FlightTypeResource::collection($this->flightTypes),
        ];
    }
}