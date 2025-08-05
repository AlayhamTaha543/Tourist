<?php

namespace App\Repositories\Impl;

use App\Http\Requests\Travel\TravelBookingRequest;
use App\Models\Booking;
use App\Models\DiscountPoint;
use App\Models\Promotion;
use App\Models\TravelAgency;
use App\Models\TravelBooking;
use App\Models\TravelFlight;
use App\Models\Favourite;
use App\Models\Policy;
use App\Models\UserRank;
use App\Repositories\Interfaces\TravelInterface;
use App\Traits\ApiResponse;
use App\Traits\HandlesUserPoints;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class TravelRepository implements TravelInterface
{
    use ApiResponse , HandlesUserPoints;

    public function getAllFlights()
    {
        $flights = TravelFlight::with(['agency', 'departure', 'arrival'])->get();

        $result = $flights->map(function ($flight) {
        $user = auth()->user();
        $isFavourited = false;

        if ($user) {
            $isFavourited = Favourite::where([
                'user_id' => $user->id,
                'favoritable_id' => $flight->id,
                'favoritable_type' => TravelFlight::class,
            ])->exists();
            }
            $now = now();
            $promotion = Promotion::where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('applicable_type', 1)
                ->orwhere('applicable_type', 6)
                ->first();

            return [
                'flight' => $flight,
                'is_favourited' => $isFavourited,
                'promotion' => $promotion ? [
                    'promotion_code' => $promotion->promotion_code,
                    'description' => $promotion->description,
                    'discount_type' => $promotion->discount_type,
                    'discount_value' => $promotion->discount_value,
                    'minimum_purchase' => $promotion->minimum_purchase,
                ] : null,
            ];
        });

        return $this->success('All flights retrieved successfully', [
            'flights' => $result,
        ]);
    }

    public function getFlight($id)
    {
        $flight = TravelFlight::with(['agency', 'departure', 'arrival'])->find($id);

        if (!$flight) {
            return $this->error('Flight not found', 404);
        }

        $user = auth()->user();
        $isFavourited = false;

        if ($user) {
            $isFavourited = Favourite::where([
                'user_id' => $user->id,
                'favoritable_id' => $flight->id,
                'favoritable_type' => TravelFlight::class,
            ])->exists();
        }
        $now = now();
        $promotion = Promotion::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('applicable_type', 1)
            ->orwhere('applicable_type', 6)
            ->first();

        return $this->success('Flight retrieved successfully', [
            'flight' => $flight,
            'is_favourited' => $isFavourited,
            'promotion' => $promotion ? [
                'promotion_code' => $promotion->promotion_code,
                'description' => $promotion->description,
                'discount_type' => $promotion->discount_type,
                'discount_value' => $promotion->discount_value,
                'minimum_purchase' => $promotion->minimum_purchase,
            ] : null,
        ]);
    }

    public function getAvailableFlights()
    {
        $now = now();

        $flights = TravelFlight::where('departure_time', '>=', $now)
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time', 'asc')
            ->with(['agency', 'departure', 'arrival'])
            ->get();

        return $this->success('Upcoming available flights retrieved', $flights);
    }
    public function getAvailableFlightsDate(Request $request)
    {
        $request->validate([
            'time' => 'required|date',
        ]);

        $time = Carbon::parse($request->time);

        $flights = TravelFlight::where('departure_time', '>=', $time)
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time', 'asc')
            ->with(['agency', 'departure', 'arrival'])
            ->get();

        return $this->success('Available flights from selected time retrieved', $flights);
    }
    public function getAgency($id)
    {
        $agency = TravelAgency::where('id', $id)
            ->with(['location', 'flights','admin'])
            ->get();
        $policies = Policy::where('service_type', 2)->get()->map(function ($policy) {
            return [
                'policy_type' => $policy->policy_type,
                'cutoff_time' => $policy->cutoff_time,
                'penalty_percentage' => $policy->penalty_percentage,
            ];
        });
        return $this->success('Agency retrieved', [
            'agency' => $agency,
            'policies' => $policies,
        ]);
    }
    public function getAllAgency()
    {
        $agency = TravelAgency::with(['location', 'flights','admin'])
            ->get();

        return $this->success('agency by agency retrieved', $agency);
    }

    public function bookFlightByPoint($id, TravelBookingRequest   $request){
        $flight = TravelFlight::find($id);

        if (!$flight) {
            return $this->error('Flight not found', 404);
        }

        if ($flight->departure_time <= now()) {
            return $this->error('Cannot book a flight that has already departed.', 400);
        }

        $already_booked_seats = TravelBooking::where('flight_id', $flight->id)
            ->where('status', '!=', 'cancelled')
            ->sum('number_of_people');

        $remaining_seats = $flight->available_seats - $already_booked_seats;

        if ($request->number_of_people > $remaining_seats) {
            return $this->error('Not enough available seats. Only ' . $remaining_seats . ' remaining.', 400);
        }

        $user = auth('sanctum')->user();
        $user_rank = $user->rank ?? new UserRank(['user_id' => $user->id]);
        $user_points = $user_rank->points_earned ?? 0;

        $rule = DiscountPoint::where('action', 'book_flight')->first();

        if (!$rule || $user_points < $rule->required_points) {
            return $this->error('You do not have enough reward points to book this flight. Minimum required: ' . ($rule->required_points ?? 'N/A'), 403);
        }

        $discount = ($flight->price * $request->number_of_people) * ($rule->discount_percentage / 100);
        $total_cost = ($flight->price * $request->number_of_people) - $discount;

        $return_flight = null;
        if ($request->ticket_type === 'round_trip') {
            $return_flight = TravelFlight::where('departure_id', $flight->arrival_id)
                ->where('arrival_id', $flight->departure_id)
                ->where('departure_time', '>', $flight->arrival_time)
                ->where('status', 'scheduled')
                ->orderBy('departure_time', 'asc')
                ->first();

            if (!$return_flight) {
                return $this->error('No return flight available for this route.', 400);
            }

            if ($return_flight->departure_time <= $flight->arrival_time) {
                return $this->error('Return flight must be after the departure flight ends.', 400);
            }

            $return_seats = TravelBooking::where('flight_id', $return_flight->id)
                ->where('status', '!=', 'cancelled')
                ->sum('number_of_people');

            $return_remaining = $return_flight->available_seats - $return_seats;

            if ($request->number_of_people > $return_remaining) {
                return $this->error("Not enough return flight seats. Only $return_remaining remaining.", 400);
            }
        }

        $booking = Booking::create([
            'booking_reference' => 'FB-' . strtoupper(uniqid()),
            'user_id' => $user->id,
            'booking_type' => 2,
            'total_price' => $total_cost,
            'payment_status' => 1,
        ]);

        $travelBooking = TravelBooking::create([
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'flight_id' => $flight->id,
            'ticket_type' => $request->ticket_type,
            'number_of_people' => $request->number_of_people,
            'booking_date' => now()->toDateString(),
            'total_price' => $total_cost,
            'discount_amount' => $discount,
            'payment_status' => 1,
            'status' => 'confirmed',
        ]);

        if ($return_flight) {
            TravelBooking::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'flight_id' => $return_flight->id,
                'ticket_type' => 'return',
                'number_of_people' => $request->number_of_people,
                'booking_date' => now()->toDateString(),
                'total_price' => 0,
                'discount_amount' => 0,
                'payment_status' => 1,
                'status' => 'confirmed',
            ]);
        }

        $user_rank->points_earned -= $rule->required_points;
        $user_rank->save();

        return $this->success('Flight booked successfully with discount applied.', [
            'booking_reference' => $booking->booking_reference,
            'reservation_id' => $travelBooking->id,
            'flight_id' => $flight->id,
            'departure_time' => $flight->departure_time,
            'return_flight_id' => $return_flight?->id,
            'return_departure_time' => $return_flight?->departure_time,
            'total_cost' => $total_cost,
            'discount_applied' => true,
            'discount_amount' => $discount,
        ]);
    }

    public function bookFlight($id, TravelBookingRequest $request){
        $flight = TravelFlight::find($id);
        if (!$flight) {
            return $this->error('Flight not found', 404);
        }
        if ($flight->departure_time <= now()) {
            return $this->error('Cannot book a flight that has already departed.', 400);
        }
        $already_booked_seats = TravelBooking::where('flight_id', $flight->id)
            ->where('status', '!=', 'cancelled')
            ->sum('number_of_people');
        $remaining_seats = $flight->available_seats - $already_booked_seats;
        if ($request->number_of_people > $remaining_seats) {
            return $this->error('Not enough available seats. Only ' . $remaining_seats . ' remaining.', 400);
        }

        $return_flight = null;
        if ($request->ticket_type === 'round_trip') {
            $return_flight = TravelFlight::where('departure_id', $flight->arrival_id)
                ->where('arrival_id', $flight->departure_id)
                ->where('departure_time', '>', $flight->arrival_time)
                ->where('status', 'scheduled')
                ->orderBy('departure_time', 'asc')
                ->first();
            if (!$return_flight) {
                return $this->error('No return flight available for this route.', 400);
            }
            if ($return_flight->departure_time <= $flight->arrival_time) {
                return $this->error('Return flight must be after the departure flight ends.', 400);
            }
            $return_seats = TravelBooking::where('flight_id', $return_flight->id)
                ->where('status', '!=', 'cancelled')
                ->sum('number_of_people');
            $return_remaining = $return_flight->available_seats - $return_seats;
            if ($request->number_of_people > $return_remaining) {
                return $this->error("Not enough return flight seats. Only $return_remaining remaining.", 400);
            }
        }

        $booking_reference = 'FB-' . strtoupper(uniqid());
        $total_cost = $flight->price * $request->number_of_people;

        if ($return_flight) {
            $total_cost += $return_flight->price * $request->number_of_people;
        }

        $promotion = null;
        $promotion_code = $request->promotion_code;

        if ($promotion_code) {
            $promotion = Promotion::where('promotion_code', $promotion_code)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(function ($q) {
                    $q->where('applicable_type', 1)
                    ->orWhere('applicable_type', 7);
                })
                ->first();

            if (!$promotion) {
                return $this->error('Invalid or expired promotion code', 400);
            }
            if ($total_cost < $promotion->minimum_purchase) {
                return $this->error("Total must be at least {$promotion->minimum_purchase} to use this code.", 400);
            }
            if (!in_array($promotion->applicable_type, [null, 1, 7])) {
                return $this->error('This code cannot be applied to this flight booking', 400);
            }
        }

        $discount_amount = 0;
        if ($promotion) {
            $discount_amount = $promotion->discount_type == 1
                ? ($total_cost * $promotion->discount_value / 100)
                : $promotion->discount_value;
            $discount_amount = min($discount_amount, $total_cost);
        }

        $totalCost_afterDiscount = $total_cost - $discount_amount;

        $booking = Booking::create([
            'booking_reference' => $booking_reference,
            'user_id' => auth('sanctum')->id(),
            'booking_type' => 2,
            'total_price' => $totalCost_afterDiscount,
            'payment_status' => 1,
        ]);

        if (!$booking) {
            return $this->error('Failed to create booking', 500);
        }

        $travel_booking = TravelBooking::create([
            'user_id' => auth('sanctum')->id(),
            'booking_id' => $booking->id,
            'flight_id' => $flight->id,
            'ticket_type' => $request->ticket_type,
            'number_of_people' => $request->number_of_people,
            'booking_date' => now()->toDateString(),
            'total_price' => $totalCost_afterDiscount,
            'discount_amount' => $discount_amount,
            'status' => 'confirmed',
        ]);

        if ($return_flight) {
            TravelBooking::create([
                'user_id' => auth('sanctum')->id(),
                'booking_id' => $booking->id,
                'flight_id' => $return_flight->id,
                'ticket_type' => 'return',
                'number_of_people' => $request->number_of_people,
                'booking_date' => now()->toDateString(),
                'total_price' => $return_flight->price * $request->number_of_people,
                'discount_amount' => 0,
                'status' => 'confirmed',
            ]);
        }

        if ($promotion) {
            $promotion->increment('current_usage');
        }

        $this->addPointsFromAction(auth('sanctum')->user(), 'book_flight', $request->number_of_people);

        return $this->success('Flight booked successfully', [
            'booking_reference' => $booking->booking_reference,
            'reservation_id' => $travel_booking->id,
            'flight_id' => $flight->id,
            'departure_time' => $flight->departure_time,
            'total_cost' => $totalCost_afterDiscount,
            'discount_amount' => $discount_amount,
        ]);
    }
}
