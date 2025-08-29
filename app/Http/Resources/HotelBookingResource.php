<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HotelBookingResource extends JsonResource
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
            'hotel' => [
                'name' => optional(optional(optional($this->hotelBooking)->roomType)->hotel)->name,
                'location' => optional(optional(optional($this->hotelBooking)->roomType)->hotel)->location->name ?? null,
            ],
            'room_type' => optional(optional($this->hotelBooking)->roomType)->name ?? null,
            'check_in_date' => optional($this->hotelBooking)->check_in_date ?? null,
            'check_out_date' => optional($this->hotelBooking)->check_out_date ?? null,
        ];
    }
}