<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TaxiBookingResource;
use App\Http\Resources\TourBookingResource;
use App\Http\Resources\HotelBookingResource;
use App\Http\Resources\TravelBookingResource;
use App\Http\Resources\RestaurantBookingResource;
use App\Http\Resources\RentalBookingResource;

class BookingCollectionResource extends ResourceCollection
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
                        Log::warning('Booking with ID ' . $booking->id . ' (Reference: ' . $booking->booking_reference . ') has booking_type "taxi" but no associated TaxiBooking record.');
                        continue 2;
                    }
                    $response['taxi'][] = new TaxiBookingResource($booking);
                    break;
                case 'tour':
                    if (is_null($booking->tourBooking)) {
                        Log::warning('Booking with ID ' . $booking->id . ' (Reference: ' . $booking->booking_reference . ') has booking_type "tour" but no associated TourBooking record.');
                        continue 2;
                    }
                    $response['tour'][] = new TourBookingResource($booking);
                    break;
                case 'hotel':
                    if (is_null($booking->hotelBooking)) {
                        Log::warning('Booking with ID ' . $booking->id . ' (Reference: ' . $booking->booking_reference . ') has booking_type "hotel" but no associated HotelBooking record.');
                        continue 2;
                    }
                    $response['hotel'][] = new HotelBookingResource($booking);
                    break;
                case 'travel':
                    if (is_null($booking->travelBooking)) {
                        Log::warning('Booking with ID ' . $booking->id . ' (Reference: ' . $booking->booking_reference . ') has booking_type "travel" but no associated TravelBooking record.');
                        continue 2;
                    }
                    $response['flight'][] = new TravelBookingResource($booking);
                    break;
                case 'restaurant':
                    if (is_null($booking->restaurantBooking)) {
                        Log::warning('Booking with ID ' . $booking->id . ' (Reference: ' . $booking->booking_reference . ') has booking_type "restaurant" but no associated RestaurantBooking record.');
                        continue 2;
                    }
                    $response['restaurant'][] = new RestaurantBookingResource($booking);
                    break;
                case 'rental':
                    if (is_null($booking->rentalBooking)) {
                        Log::warning('Booking with ID ' . $booking->id . ' (Reference: ' . $booking->booking_reference . ') has booking_type "rental" but no associated RentalBooking record.');
                        continue 2;
                    }
                    $response['taxi'][] = new RentalBookingResource($booking);
                    break;
            }
        }

        return $response;
    }
}
