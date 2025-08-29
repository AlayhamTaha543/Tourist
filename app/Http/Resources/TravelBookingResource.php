<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TravelBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'booking_reference' => $this->booking_reference,
            'date' => $this->booking_date,
            'total_price' => $this->total_price,
            'flight' => [
                'flight_number' => optional(optional($this->travelBooking)->flight)->flight_number ?? null,
                'departure_location' => optional(optional(optional($this->travelBooking)->flight)->departure)->name ?? null,
                'arrival_location' => optional(optional(optional($this->travelBooking)->flight)->arrival)->name ?? null,
            ],
            'number_of_people' => optional($this->travelBooking)->number_of_people ?? 0,
        ];
    }
}