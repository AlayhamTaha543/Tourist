<?php
namespace App\Repositories\Impl;

use App\Http\Requests\Api\Auth\PayRequest;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\Payment;
use App\Models\Policy;
use App\Models\RestaurantBooking;
use App\Models\TourBooking;
use App\Models\TravelBooking;
use App\Notifications\PaymentNotification;
use App\Repositories\Interfaces\BookingInterface;
use App\Services\TaxiBooking\TaxiBookingService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class BookingRepository implements BookingInterface
{
    use ApiResponse;
    protected TaxiBookingService $taxiBooking;
    public function payForBooking($id, PayRequest $request)
    {
        $request->validated();
        $booking = Booking::find($id);
        if (!$booking) {
            return $this->error('Booking not found', 404);
        }

        if ($booking->paymentStatus == 'Success') {
            return $this->error('This booking is already paid', 400);
        }

        $amount = $request->amount;

        if ($amount < $booking->totalPrice) {
            return $this->error('Paid amount is less than required total price', 400);
        }

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
            'amount' => $amount,
            'payment_date' => now(),
            'payment_method' => $request->paymentMethod,
            'transaction_id' => 'FAKE-' . strtoupper(Str::random(8)),
            'status' => 'Success',
            'gateway_response' => json_encode(['message' => 'Fake payment completed']),
        ]);

        $booking->update(['paymentStatus' => 'Success']);

        $user = $booking->user;
        $user->notify(new PaymentNotification(
            $payment->amount,
            $payment->payment_method,
            $payment->payment_reference
        ));
        return $this->success('Payment completed successfully', [
            'bookingReference' => $booking->booking_reference,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'payment_reference' => $payment->payment_reference,
        ]);
    }
    public function getBookingHistory()
    {
        $user = auth('sanctum')->user();
        $bookings = Booking::with([
            'tourBooking.tour',
            'hotelBooking.hotel',
            'restaurantBooking.restaurant',
            'travelBooking.flight',
            'taxiBooking.taxiService'
        ])
            ->where('user_id', $user->id)
            ->where('status', operator: 'pending')//          ====================STATUS======================
            ->get();
        $response = [
            'tour' => [],
            'hotel' => [],
            'restaurant' => [],
            'flight' => [],
            'taxi' => [],
        ];
        foreach ($bookings as $booking) {
            switch ($booking->booking_type) {
                case 'Taxi':
                    $response['taxi'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'taxi' => optional($booking->taxiBooking->taxiService)->only(['id', 'name', 'location']),
                        'pickup_location' => $booking->taxiBooking->pickupLocation ?? null,
                        'dropoff_location' => $booking->taxiBooking->dropoffLocation ?? null,
                        'pickup_time' => $booking->taxiBooking->pickup_date_time ?? null,
                    ];
                    break;
                case 'tour':
                    $response['tour'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'tour' => optional($booking->tourBooking->tour)->only(['id', 'name', 'location']),
                        'schedule' => optional($booking->tourBooking->schedule),
                        'number_of_adults' => $booking->tourBooking->number_of_adults ?? 0,
                        'number_of_children' => $booking->tourBooking->number_of_children ?? 0,
                    ];
                    break;
                case 'hotel':
                    $response['hotel'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'hotel' => optional(optional(optional($booking->hotelBooking)->roomType)->hotel)?->only(['id', 'name', 'location']),
                        'room_type' => optional(optional($booking->hotelBooking)->roomType)->name ?? null,
                        'check_in_date' => optional($booking->hotelBooking)->check_in_date ?? null,
                        'check_out_date' => optional($booking->hotelBooking)->check_out_date ?? null,
                    ];
                    break;
                case 'package':
                    $response['flight'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'flight' => optional($booking->travelBooking->flight),
                        'number_of_people' => $booking->travelBooking->number_of_people ?? 0,
                    ];
                    break;
                case 'restaurant':
                    $orderItems = [];
                    if (!empty($booking->restaurantBooking->order)) {
                        $orderItems = json_decode($booking->restaurantBooking->order, true);
                    }

                    $response['restaurant'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'restaurant' => optional($booking->restaurantBooking->restaurant)?->only(['id', 'name', 'location']),
                        'reservation_date' => $booking->restaurantBooking->reservation_date ?? null,
                        'reservation_time' => $booking->restaurantBooking->reservation_time ?? null,
                        'guests' => $booking->restaurantBooking->number_of_guests ?? 0,
                        'order' => $orderItems,
                    ];
                    break;
            }
        }

        return $this->success('Booking history retrieved successfully', $response);
    }
    public function getAllBookings()
    {
        $user = auth('sanctum')->user();
        // dd(Booking::where('user_id', $user->id)->get());

        $bookings = Booking::with([
            'tourBooking.tour',
            'tourBooking.schedule',
            'hotelBooking.roomType.hotel',
            'restaurantBooking.restaurant',
            'travelBooking.flight',
        ])
            ->where('user_id', $user->id)
            ->get();

        $response = [
            'tour' => [],
            'hotel' => [],
            'restaurant' => [],
            'flight' => [],
            'taxi' => [],
        ];

        foreach ($bookings as $booking) {
            switch ($booking->booking_type) {
                case 'Taxi':
                    $response['taxi'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'taxi' => optional($booking->taxiBooking->taxiService)->only(['id', 'name', 'location']),
                        'pickup_location' => $booking->taxiBooking->pickupLocation ?? null,
                        'dropoff_location' => $booking->taxiBooking->dropoffLocation ?? null,
                        'pickup_time' => $booking->taxiBooking->pickup_date_time ?? null,
                    ];
                    break;
                case 'tour':
                    $response['tour'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'tour' => optional($booking->tourBooking->tour)->only(['id', 'name', 'location']),
                        'schedule' => optional($booking->tourBooking->schedule),
                        'number_of_adults' => $booking->tourBooking->number_of_adults ?? 0,
                        'number_of_children' => $booking->tourBooking->number_of_children ?? 0,
                    ];
                    break;
                case 'hotel':
                    $response['hotel'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'hotel' => optional(optional(optional($booking->hotelBooking)->roomType)->hotel)?->only(['id', 'name', 'location']),
                        'room_type' => optional(optional($booking->hotelBooking)->roomType)->name ?? null,
                        'check_in_date' => optional($booking->hotelBooking)->check_in_date ?? null,
                        'check_out_date' => optional($booking->hotelBooking)->check_out_date ?? null,
                    ];
                    break;
                case 'package':
                    $response['flight'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'flight' => optional($booking->travelBooking->flight),
                        'number_of_people' => $booking->travelBooking->number_of_people ?? 0,
                    ];
                    break;
                case 'restaurant':
                    $orderItems = [];
                    if (!empty($booking->restaurantBooking->order)) {
                        $orderItems = json_decode($booking->restaurantBooking->order, true);
                    }

                    $response['restaurant'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'restaurant' => optional($booking->restaurantBooking->restaurant)?->only(['id', 'name', 'location']),
                        'reservation_date' => $booking->restaurantBooking->reservation_date ?? null,
                        'reservation_time' => $booking->restaurantBooking->reservation_time ?? null,
                        'guests' => $booking->restaurantBooking->number_of_guests ?? 0,
                        'order' => $orderItems,
                    ];
                    break;
            }
        }

        return $this->success('All bookings retrieved successfully', $response);
    }
    public function cancelBooking($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return $this->error('Booking not found', 404);
        }

        $serviceTypeMap = [
            1 => 'hotel',
            2 => 'flight',
            3 => 'restaurant',
            4 => 'tour',
        ];

        $serviceType = $serviceTypeMap[$booking->booking_type] ?? null;
        if (!$serviceType) {
            return $this->error('Invalid booking type', 400);
        }

        $policy = Policy::where('service_type', $serviceType)
            ->where('policy_type', 'cancel')
            ->first();

        if (!$policy) {
            return $this->error('Cancellation policy not found for this service', 400);
        }

        $bookingTime = null;
        switch ($booking->booking_type) {
            case 1:
                $hotelBooking = HotelBooking::where('booking_id', $booking->id)->first();
                $bookingTime = $hotelBooking?->check_in_date;
                break;
            case 2:
                $flightBooking = TravelBooking::where('booking_id', $booking->id)->first();
                $bookingTime = $flightBooking?->departure_time;
                break;
            case 3:
                $restaurantBooking = RestaurantBooking::where('booking_id', $booking->id)->first();
                if ($restaurantBooking) {
                    $bookingTime = $restaurantBooking->reservation_date . ' ' . $restaurantBooking->reservation_time;
                }
                break;
            case 4:
                $tourBooking = TourBooking::where('booking_id', $booking->id)->first();
                $bookingTime = $tourBooking?->tour_date;
                break;
        }

        if (!$bookingTime) {
            return $this->error('Relevant booking time not found', 400);
        }

        $hoursBefore = now()->diffInHours($bookingTime, false);
        if ($hoursBefore < $policy->cutoff_time) {
            $penalty = ($booking->total_price * $policy->penalty_percentage) / 100;
            return $this->error("Cancellation not allowed less than {$policy->cutoff_time} hours before. Penalty: {$penalty}", 400);
        }

        switch ($booking->booking_type) {
            case 1:
                HotelBooking::where('booking_id', $booking->id)->delete();
                break;
            case 2:
                TravelBooking::where('booking_id', $booking->id)->delete();
                break;
            case 3:
                RestaurantBooking::where('booking_id', $booking->id)->delete();
                break;
            case 4:
                TourBooking::where('booking_id', $booking->id)->delete();
                break;
        }

        $booking->delete();

        return $this->success('Booking cancelled successfully, penalty applied: 0');
    }
    public function modifyBooking(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return $this->error('Booking not found', 404);
        }

        $policy = Policy::where('service_type', $booking->booking_type)
            ->where('policy_type', 'modify')
            ->first();

        if (!$policy) {
            return $this->error('Modification policy not found for this service', 400);
        }

        $bookingTime = null;

        switch ($booking->booking_type) {
            case 1:
                $hotelBooking = HotelBooking::where('booking_id', $booking->id)->first();
                $bookingTime = optional($hotelBooking)->check_in_date;
                break;
            case 2:
                $travelBooking = TravelBooking::where('booking_id', $booking->id)->first();
                $bookingTime = optional($travelBooking?->flight)->departure_time;
                break;
            case 3:
                $restaurantBooking = RestaurantBooking::where('booking_id', $booking->id)->first();
                if ($restaurantBooking) {
                    $bookingTime = $restaurantBooking->reservation_date . ' ' . $restaurantBooking->reservation_time;
                }
                break;
            case 4:
                $tourBooking = TourBooking::where('booking_id', $booking->id)->first();
                $bookingTime = optional($tourBooking)->tour_date;
                break;
        }

        if (!$bookingTime) {
            return $this->error('Booking time not found', 400);
        }

        $hoursBefore = now()->diffInHours($bookingTime, false);

        if ($hoursBefore < $policy->cutoff_time) {
            $penalty = round($booking->total_price * $policy->penalty_percentage / 100, 2);
            return $this->error("Modification not allowed less than {$policy->cutoff_time} hours before. Penalty: {$penalty}", 400);
        }

        switch ($booking->booking_type) {
            case 1:
                $hotelBooking = HotelBooking::where('booking_id', $booking->id)->first();
                $hotelBooking->check_in_date = $request->check_in_date ?? $hotelBooking->check_in_date;
                $hotelBooking->check_out_date = $request->check_out_date ?? $hotelBooking->check_out_date;
                $hotelBooking->save();
                break;

            case 2:
                $travelBooking = TravelBooking::where('booking_id', $booking->id)->first();
                $travelBooking->number_of_people = $request->number_of_people ?? $travelBooking->number_of_people;
                $travelBooking->flight_class = $request->flight_class ?? $travelBooking->flight_class;
                $travelBooking->save();
                break;

            case 3:
                $restaurantBooking = RestaurantBooking::where('booking_id', $booking->id)->first();
                $restaurantBooking->number_of_people = $request->number_of_people ?? $restaurantBooking->number_of_people;
                $restaurantBooking->reservation_time = $request->reservation_time ?? $restaurantBooking->reservation_time;
                $restaurantBooking->save();
                break;

            case 4:
                $tourBooking = TourBooking::where('booking_id', $booking->id)->first();
                $tourBooking->tour_date = $request->tour_date ?? $tourBooking->tour_date;
                $tourBooking->save();
                break;
        }

        return $this->success('Booking modified successfully');
    }
}