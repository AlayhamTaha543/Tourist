<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TourBookingResource extends JsonResource
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
            'tour' => [
                'name' => optional($this->tourBooking->tour)->name,
                'location' => optional(optional($this->tourBooking->tour)->location)->name ?? null,
            ],
            'schedule' => optional($this->tourBooking->schedule),
            'number_of_adults' => $this->tourBooking->number_of_adults ?? 0,
            'number_of_children' => $this->tourBooking->number_of_children ?? 0,
        ];
    }
}
