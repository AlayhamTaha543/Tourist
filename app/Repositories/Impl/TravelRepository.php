<?php

namespace App\Repositories\Impl;

use App\Http\Requests\Travel\TravelBookingRequest;
use App\Http\Resources\NearestFlightResource;
use App\Models\Booking;
use App\Models\DiscountPoint;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\TravelAgency;
use App\Models\TravelBooking;
use App\Models\TravelFlight;
use App\Models\Favourite;
use App\Models\Policy;
use App\Models\Rating;
use App\Models\User;
use App\Models\UserRank;
use App\Repositories\Interfaces\TravelInterface;
use App\Traits\ApiResponse;
use App\Traits\HandlesUserPoints;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ServiceInterface;
use App\Http\Resources\FlightResource;
use App\Http\Resources\FlightTourResource;
use App\Http\Resources\PromotionResource;
use App\Models\Tour;


class TravelRepository implements TravelInterface
{
    use ApiResponse, HandlesUserPoints;

    protected $serviceRepository;

    public function __construct(ServiceInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Helper method to determine if a flight is favorited by the user.
     */
    private function getIsFavourited(?User $user, TravelFlight $flight): bool
    {
        if (!$user) {
            return false;
        }

        return Favourite::where([
            'user_id' => $user->id,
            'favoritable_id' => $flight->id,
            'favoritable_type' => TravelFlight::class,
        ])->exists();
    }

    /**
     * Helper method to get the active promotion.
     */
    private function getPromotion(): ?Promotion
    {
        $now = now();
        return Promotion::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('applicable_type', 1)
            ->orWhere('applicable_type', 6)
            ->first();
    }

    /**
     * Helper method to calculate the average rating for a flight.
     */
    private function getAverageFlightRating(int $flightId): float
    {
        return Rating::where('rateable_id', $flightId)
            ->where('rateable_type', 'flight')
            ->where('is_visible', true)
            ->avg('rating') ?? 0;
    }

    /**
     * Helper method to format location strings.
     */
    private function formatLocation($location): string
    {
        return $location->name . ', ' .
            ($location->city->name ?? '') . ', ' .
            ($location->city->country->name ?? '');
    }

    /**
     * Helper method to get and format tours associated with the flight's arrival.
     */
    private function getAssociatedTours(TravelFlight $flight): \Illuminate\Support\Collection
    {
        // Get tours directly associated with the arrival location
        $toursByLocation = Tour::with(['admin', 'schedules'])
            ->where('location_id', $flight->arrival_id)
            ->get();

        // Get tours associated with locations in the arrival country
        $toursByCountry = collect();
        if ($flight->arrival->city->country) {
            $arrivalCountryId = $flight->arrival->city->country->id;
            $toursByCountry = Tour::with(['admin', 'schedules'])
                ->whereHas('location', function ($query) use ($arrivalCountryId) {
                    $query->whereHas('city.country', function ($subQuery) use ($arrivalCountryId) {
                        $subQuery->where('id', $arrivalCountryId);
                    });
                })
                ->get();
        }

        // Merge and get unique tours
        return $toursByLocation->merge($toursByCountry)->unique('id');
    }

    public function getAllFlights()
    {
        // Get current time plus 6 hours
        $minimumDepartureTime = now()->addHours(6);

        $flights = TravelFlight::with(['departure.city.country', 'arrival.city.country'])
            ->where('departure_time', '>=', $minimumDepartureTime)
            ->get();

        $result = $flights->map(function ($flight) {
            $user = auth('sanctum')->user();
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

            // Calculate average rating for this flight
            $averageRating = Rating::where('rateable_id', $flight->id)
                ->where('rateable_type', 'flight') // Assuming you have a rating_type for flights
                ->where('is_visible', true)
                ->avg('rating') ?? 0;

            // Format departure and arrival locations
            $departureLocation =
                substr($flight->departure->city->name, 0, 1) .
                ($flight->departure->city->country->code ?? '');

            $arrivalLocation =
                substr($flight->arrival->city->name, 0, 1) .
                ($flight->arrival->city->country->code ?? '');

            return [
                [
                    'id' => $flight->id,
                    'flight_number' => $flight->flight_number,
                    'departure' => $departureLocation,
                    'arrival' => $arrivalLocation,
                    'departure_time' => $flight->departure_time,
                    'arrival_time' => $flight->arrival_time,
                    'duration_minutes' => $flight->duration_minutes,
                    'price' => $flight->price,
                    'available_seats' => $flight->available_seats,
                    'is_popular' => $flight->is_popular,
                    'status' => $flight->status,
                    'rating' => round($averageRating, 1), // Round to 1 decimal place
                    'flight_types' => $flight->flightTypes->map(function ($type) {
                        return [
                            'flight_type' => $type->flight_type,
                            'price' => $type->price,
                            'available_seats' => $type->available_seats,
                        ];
                    }),
                ],
                // 'is_favourited' => $isFavourited,
                // 'promotion' => $promotion ? [
                //     'promotion_code' => $promotion->promotion_code,
                //     'description' => $promotion->description,
                //     'discount_type' => $promotion->discount_type,
                //     'discount_value' => $promotion->discount_value,
                //     'minimum_purchase' => $promotion->minimum_purchase,
                // ] : null,
            ];
        });

        return $this->success('All flights retrieved successfully', [
            'flights' => $result,
        ]);
    }

    public function getFlight($id)
    {
        $flight = TravelFlight::with(['agency', 'departure.city.country', 'arrival.city.country', 'flightTypes'])->find($id);

        if (!$flight) {
            return $this->error('Flight not found', 404);
        }

        $user = auth('sanctum')->user();

        $isFavourited = $this->getIsFavourited($user, $flight);
        $promotion = $this->getPromotion();
        $averageRating = $this->getAverageFlightRating($flight->id);
        $allTours = $this->getAssociatedTours($flight);
        $userPoints = $this->serviceRepository->getUserPoints();

        return $this->success('Flight retrieved successfully', [
            'flight' => new FlightResource($flight->setAttribute('rating', $averageRating)),
            'tours' => FlightTourResource::collection($allTours),
            'is_favourited' => $isFavourited,
            'promotion' => $promotion ? new PromotionResource($promotion) : null,
            'user_points' => $userPoints ?? 0,
            'user_name' => $user ? $user->first_name . ' ' . $user->last_name : 'user',
            'location_name' => $user && $user->location ? $user->location : 'location',
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
            ->with(['location', 'flights', 'admin'])
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
        $agency = TravelAgency::with(['location', 'flights', 'admin'])
            ->get();

        return $this->success('agency by agency retrieved', $agency);
    }

    public function bookFlightByPoint($id, TravelBookingRequest $request)
    {
        $flight = TravelFlight::find($id);

        if (!$flight) {
            return $this->error('Flight not found', 404);
        }

        if ($flight->departure_time <= now()) {
            return $this->error('Cannot book a flight that has already departed.', 400);
        }

        $flightType = $flight->flightTypes()->where('flight_type', $request->flight_type_name)->first();

        if (!$flightType) {
            return $this->error('Flight type not available for this flight.', 400);
        }

        if ($flightType->available_seats < 1) {
            return $this->error('Not enough available seats for this flight type. Only ' . $flightType->available_seats . ' remaining.', 400);
        }

        $user = auth('sanctum')->user();
        // Ensure user_rank exists or create a new one
        $user_rank = UserRank::firstOrCreate(
            ['user_id' => $user->id],
            ['points_earned' => 0] // Default points if new
        );
        $user_points = $user_rank->points_earned ?? 0;

        $passportImagePath = null;
        if ($request->hasFile('passport_image')) {
            $passportImagePath = $request->file('passport_image')->store('passports', 'public');
        }

        // Retrieve the most applicable discount rule based on user's points
        $rule = DiscountPoint::where('action', 'book_flight')
            ->where('min_points', '<=', $user_points)
            ->orderByDesc('min_points')
            ->first();

        if (!$rule) {
            return $this->error('You do not have enough reward points to qualify for a discount.', 403);
        }

        $original_total_cost = $flightType->price;
        $return_flight = null;
        $returnFlightType = null;

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

            $returnFlightType = $return_flight->flightTypes()->where('flight_type', $request->flight_type_name)->first();

            if (!$returnFlightType || $returnFlightType->available_seats < 1) {
                return $this->error("Not enough return flight seats for this flight type. Only " . ($returnFlightType->available_seats ?? 0) . " remaining.", 400);
            }
            $original_total_cost += $returnFlightType->price;
        }

        $discount_amount = ($original_total_cost) * ($rule->discount_percentage / 100);
        $total_cost_after_discount = ($original_total_cost) - $discount_amount;

        $booking = Booking::create([
            'booking_reference' => 'FB-' . strtoupper(uniqid()),
            'user_id' => $user->id,
            'booking_type' => 2,
            'total_price' => $total_cost_after_discount,
            'payment_status' => 1, // Assuming payment will be handled for the discounted amount
        ]);

        $travelBooking = TravelBooking::create([
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'flight_id' => $flight->id,
            'ticket_type' => $request->ticket_type,
            'number_of_people' => 1, // Always 1 person per booking
            'booking_date' => now()->toDateString(),
            'total_price' => $total_cost_after_discount, // This should be the discounted price for the entire booking
            'discount_amount' => $discount_amount,
            'payment_status' => 1,
            'status' => 'confirmed',
            'passport_image' => $passportImagePath,
            'flight_type_name' => $request->flight_type_name,
            'additional_bag_fines' => $request->additional_bag_fines,
        ]);

        $flightType->decrement('available_seats', 1);

        if ($return_flight) {
            TravelBooking::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'flight_id' => $return_flight->id,
                'ticket_type' => 'return',
                'number_of_people' => 1, // Always 1 person per booking
                'booking_date' => now()->toDateString(),
                'total_price' => $returnFlightType->price, // Price of the return flight, not discounted again
                'discount_amount' => 0, // Discount already applied to overall total
                'payment_status' => 1,
                'status' => 'confirmed',
                'flight_type_name' => $request->flight_type_name,
            ]);
            $returnFlightType->decrement('available_seats', 1);
        }

        $user_rank->points_earned -= $rule->required_points; // Deduct points for the applied rule
        $user_rank->save();

        return $this->success('Flight booked successfully with discount applied.', [
            'booking_reference' => $booking->booking_reference,
            'reservation_id' => $travelBooking->id,
            'flight_id' => $flight->id,
            'departure_time' => $flight->departure_time,
            'return_flight_id' => $return_flight?->id,
            'return_departure_time' => $return_flight?->departure_time,
            'total_cost' => $total_cost_after_discount,
            'discount_applied' => true,
            'discount_amount' => $discount_amount,
            'flight_type_name' => $request->flight_type_name,
        ]);
    }

    public function bookFlight($id, TravelBookingRequest $request)
    {
        $flight = TravelFlight::find($id);
        if (!$flight) {
            return $this->error('Flight not found', 404);
        }
        if ($flight->departure_time <= now()) {
            return $this->error('Cannot book a flight that has already departed.', 400);
        }

        $flightType = $flight->flightTypes()->where('flight_type', $request->flight_type_name)->first();

        if (!$flightType) {
            return $this->error('Flight type not available for this flight.', 400);
        }

        if ($flightType->available_seats < 1) {
            return $this->error('Not enough available seats for this flight type. Only ' . $flightType->available_seats . ' remaining.', 400);
        }

        $passportImagePath = null;
        if ($request->hasFile('passport_image')) {
            $passportImagePath = $request->file('passport_image')->store('passports', 'public');
        }

        $return_flight = null;
        $returnFlightType = null;
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

            $returnFlightType = $return_flight->flightTypes()->where('flight_type', $request->flight_type_name)->first();

            if (!$returnFlightType || $returnFlightType->available_seats < 1) {
                return $this->error("Not enough return flight seats for this flight type. Only " . ($returnFlightType->available_seats ?? 0) . " remaining.", 400);
            }
        }

        $booking_reference = 'FB-' . strtoupper(uniqid());
        $total_cost = $flightType->price;

        if ($return_flight) {
            $total_cost += $returnFlightType->price;
        }

        $discount_amount = 0;
        $points_deducted_for_discount = 0;
        $totalCost_afterDiscount = $total_cost;

        if ($request->boolean('discount')) {
            $user = auth('sanctum')->user();
            $userRank = UserRank::firstOrCreate(
                ['user_id' => $user->id],
                ['points_earned' => 0]
            );
            $user_points = $userRank->points_earned;

            if ($user_points < 5000) {
                return $this->error('You do not have enough points for this discount. 5000 points are required.', 403);
            }

            $discount_amount = $total_cost * 0.10; // 10% discount
            $totalCost_afterDiscount = $total_cost - $discount_amount;
            $points_deducted_for_discount = 5000;

            $userRank->points_earned -= $points_deducted_for_discount;
            $userRank->save();
        } else {
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

            if ($promotion) {
                $discount_amount = $promotion->discount_type == 1
                    ? ($total_cost * $promotion->discount_value / 100)
                    : $promotion->discount_value;
                $discount_amount = min($discount_amount, $total_cost);
            }

            $totalCost_afterDiscount = $total_cost - $discount_amount;
        }

        $booking = Booking::create([
            'booking_reference' => $booking_reference,
            'user_id' => auth('sanctum')->id(),
            'booking_type' => 'travel',
            'total_price' => $totalCost_afterDiscount,
            'payment_status' => 1,
        ]);

        if (!$booking) {
            return $this->error('Failed to create booking', 500);
        }

        Payment::create([
            'booking_id' => $booking->id,
            'payment_reference' => 'PAY-' . strtoupper(uniqid()),
            'amount' => $totalCost_afterDiscount,
            'payment_date' => now(),
            'payment_method' => 'credit_card', // or get from request
            'status' => 'completed',
        ]);

        $travel_booking = TravelBooking::create([
            'user_id' => auth('sanctum')->id(),
            'booking_id' => $booking->id,
            'flight_id' => $flight->id,
            'ticket_type' => $request->ticket_type,
            'number_of_people' => 1, // Always 1 person per booking
            'booking_date' => now()->toDateString(),
            'total_price' => $totalCost_afterDiscount,
            'discount_amount' => $discount_amount,
            'status' => 'confirmed',
            'passport_image' => $passportImagePath,
            'flight_type_name' => $request->flight_type_name,
            'additional_bag_fines' => $request->additional_bag_fines,
        ]);

        $flightType->decrement('available_seats', 1);
        $flight->decrement('available_seats', 1); // Decrement overall flight seats

        if ($return_flight) {
            TravelBooking::create([
                'user_id' => auth('sanctum')->id(),
                'booking_id' => $booking->id,
                'flight_id' => $return_flight->id,
                'ticket_type' => 'return',
                'number_of_people' => 1, // Always 1 person per booking
                'booking_date' => now()->toDateString(),
                'total_price' => $returnFlightType->price,
                'discount_amount' => 0,
                'status' => 'confirmed',
                'flight_type_name' => $request->flight_type_name,
            ]);
            $returnFlightType->decrement('available_seats', 1);
            $return_flight->decrement('available_seats', 1); // Decrement overall return flight seats
        }

        // if ($promotion) {
        //     $promotion->increment('current_usage');
        // }

        // Add points for the booking if no points were deducted for a discount
        if ($points_deducted_for_discount === 0) {
            $this->addPointsFromAction(auth('sanctum')->user(), $totalCost_afterDiscount, $discount_amount);
        }

        return $this->success('Flight booked successfully', [
            'booking_reference' => $booking->booking_reference,
            'reservation_id' => $travel_booking->id,
            'flight_id' => $flight->id,
            'departure_time' => $flight->departure_time,
            'total_cost' => $totalCost_afterDiscount,
            'discount_amount' => $discount_amount,
            'points_deducted' => $points_deducted_for_discount,
            'flight_type_name' => $request->flight_type_name,
        ]);
    }
    public function getAllBookedFlights($id)
    {
        $user = User::findOrFail($id);

        return $user->Bookings()->where('booking_type', 'travel')
            ->get();
    }


    public function getNearestBookedFlight($id)
    {
        try {
            $user = User::findOrFail($id);

            $bookedFlights = $user->Bookings()
                ->where('booking_type', 'travel')
                ->whereIn('status', ['confirmed', 'pending'])
                ->with([
                    'travelBooking.flight' => function ($query) {
                        $query->select(
                            'id',
                            'flight_number',
                            'departure_id',
                            'arrival_id',
                            'departure_time',
                            'arrival_time',
                            'duration_minutes',
                            'price',
                            'status'
                        );
                    },
                    'travelBooking.flight.arrival.city.country'
                ])
                ->get();

            if ($bookedFlights->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No confirmed or pending flight bookings found'
                ], 404);
            }

            // Filter future flights and sort by departure time
            $futureFlight = $bookedFlights
                ->filter(function ($booking) {
                    return $booking->travelBooking &&
                        $booking->travelBooking->flight &&
                        $booking->travelBooking->flight->departure_time &&
                        Carbon::parse($booking->travelBooking->flight->departure_time)->isFuture();
                })
                ->sortBy(function ($booking) {
                    return Carbon::parse($booking->travelBooking->flight->departure_time);
                })
                ->first();

            if (!$futureFlight) {
                return response()->json([
                    'success' => false,
                    'message' => 'No upcoming flights found'
                ], 404);
            }

            // Calculate time until departure
            $flight = $futureFlight->travelBooking->flight;
            $departureTime = Carbon::parse($flight->departure_time);
            $arrivalTime = Carbon::parse($flight->arrival_time);
            $timeUntilDeparture = $departureTime->diffForHumans();
            $hoursUntilDeparture = now()->diffInHours($departureTime);

            return new NearestFlightResource($futureFlight);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve nearest flight',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function searchFlights(Request $request)
    {
        $request->validate([
            'departure_id' => 'required|exists:locations,id',
            'arrival_id' => 'required|exists:locations,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $departureId = $request->input('departure_id');
        $arrivalId = $request->input('arrival_id');
        $searchDate = Carbon::parse($request->input('date'))->startOfDay();

        $flights = TravelFlight::with(['departure.city.country', 'arrival.city.country', 'flightTypes'])
            ->where('departure_id', $departureId)
            ->where('arrival_id', $arrivalId)
            ->whereDate('departure_time', $searchDate)
            ->where('departure_time', '>', Carbon::now())
            ->where('available_seats', '>', 0)
            ->get();

        if ($flights->isEmpty()) {
            return $this->error('No flights found for the given criteria.', 404);
        }

        $result = $flights->map(function ($flight) {
            $user = auth('sanctum')->user();
            $isFavourited = $this->getIsFavourited($user, $flight);
            $averageRating = $this->getAverageFlightRating($flight->id);

            return [
                'id' => $flight->id,
                'flight_number' => $flight->flight_number,
                'departure_location' => $this->formatLocation($flight->departure),
                'arrival_location' => $this->formatLocation($flight->arrival),
                'departure_time' => $flight->departure_time,
                'arrival_time' => $flight->arrival_time,
                'duration_minutes' => $flight->duration_minutes,
                'price' => $flight->price,
                'available_seats' => $flight->available_seats,
                'status' => $flight->status,
                'rating' => round($averageRating, 1),
                'is_favourited' => $isFavourited,
                'flight_types' => $flight->flightTypes->map(function ($type) {
                    return [
                        'flight_type' => $type->flight_type,
                        'price' => $type->price,
                        'available_seats' => $type->available_seats,
                    ];
                }),
            ];
        });

        return $this->success('Flights retrieved successfully', [
            'flights' => $result,
        ]);
    }
}
