<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxiBookingResource extends JsonResource
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
            'booking_reference' => $this->booking_id,
            'date' => $this->booking_date,
            'total_price' => $this->duration_minutes * $this->estimated_distance,
            'taxi' => [
                'name' => optional($this->taxiService)->name ?? null,
                'location' => optional(optional($this->taxiService)->location)->name ?? null,
            ],
            'pickup_location' => optional(optional($this->taxiBooking)->pickupLocation)->name ?? null,
            'dropoff_location' => optional(optional($this->taxiBooking)->dropoffLocation)->name ?? null,
            'pickup_time' => optional($this->taxiBooking)->pickup_date_time ?? null,
        ];
    }
}
