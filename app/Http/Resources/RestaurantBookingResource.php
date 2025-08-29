<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $orderItems = [];
        if (!empty($this->restaurantBooking->order)) {
            $orderItems = json_decode($this->restaurantBooking->order, true);
        }

        return [
            'booking_reference' => $this->booking_reference,
            'date' => $this->booking_date,
            'total_price' => $this->total_price,
            'restaurant' => [
                'name' => optional($this->restaurantBooking->restaurant)->name,
                'location' => optional(optional($this->restaurantBooking->restaurant)->location)->name ?? null,
            ],
            'reservation_date' => $this->restaurantBooking->reservation_date ?? null,
            'reservation_time' => $this->restaurantBooking->reservation_time ?? null,
            'guests' => $this->restaurantBooking->number_of_guests ?? 0,
            'order' => $orderItems,
        ];
    }
}
