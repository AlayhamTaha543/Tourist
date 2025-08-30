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
                'flight_number' => optional(optional($this->travelBooking)->flight)->flight_number ?? 'N/A',
                'departure_location' => optional(optional(optional($this->travelBooking)->flight)->departure)->city->country->code ?? 'N/A',
                'arrival_location' => optional(optional(optional($this->travelBooking)->flight)->arrival)->city->country->code ?? 'N/A',
            ],
            'number_of_people' => optional($this->travelBooking)->number_of_people ?? 0,
        ];
    }
}
