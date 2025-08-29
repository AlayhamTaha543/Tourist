<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RentalBookingResource extends JsonResource
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
            'rental_office' => [
                'name' => optional(optional(optional($this->rentalBooking)->rentalVehicle)->rentalOffice)->name ?? 'N/A',
                'location' => optional(optional(optional($this->rentalBooking)->rentalVehicle)->rentalOffice)->location->name ?? 'N/A',
            ],
            'vehicle_name' => optional(optional($this->rentalBooking)->rentalVehicle)->name ?? 'N/A',
            'pickup_date' => optional($this->rentalBooking)->pickup_date ?? 'N/A',
            'dropoff_date' => optional($this->rentalBooking)->return_date ?? 'N/A',
        ];
    }
}