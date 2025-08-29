<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;

class BookingHistoryCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = [
            'tour' => [],
            'hotel' => [],
            'restaurant' => [],
            'flight' => [],
            'taxi' => [],
            'rental' => [],
        ];

        foreach ($this->collection as $booking) {
            switch ($booking->booking_type) {
                case 'taxi':
                    if (is_null($booking->taxiBooking)) {
                        Log::warning('Booking with ID ' . $booking->id . ' (Reference: ' . $booking->booking_reference . ') has booking_type "taxi" but no associated TaxiBooking record in history.');
                        continue 2;
                    }
                    $response['taxi'][] = new TaxiBookingResource($booking);
                    break;
                case 'tour':
                    $response['tour'][] = new TourBookingResource($booking);
                    break;
                case 'hotel':
                    $response['hotel'][] = new HotelBookingResource($booking);
                    break;
                case 'travel':
                    $response['flight'][] = new TravelBookingResource($booking);
                    break;
                case 'restaurant':
                    $response['restaurant'][] = new RestaurantBookingResource($booking);
                    break;
                case 'rental':
                    $response['rental'][] = new RentalBookingResource($booking);
                    break;
            }
        }

        return $response;
    }
}
