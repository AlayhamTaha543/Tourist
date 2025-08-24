<?php

namespace App\Repositories\Impl;

use App\Http\Requests\Tour\TourBookingRequest;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\DiscountPoint;
use App\Models\Favourite;
use App\Models\Payment;
use App\Models\Policy;
use App\Models\Promotion;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use App\Models\TourBooking;
use App\Models\UserRank;
use App\Repositories\Interfaces\TourInterface;
use App\Traits\ApiResponse;
use App\Traits\HandlesUserPoints;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TourRepository implements TourInterface
{
    use ApiResponse, HandlesUserPoints;

    public function showAllTour()
    {
        $tours = Tour::with(['location.city.country', 'admin', 'schedules'])
            ->whereHas('schedules', function ($query) {
                $query->where('start_date', '>=', Carbon::now());
            })
            ->get();

        $result = $tours->map(function ($tour) {
            // Get the full location name using the fullName method
            $locationName = $tour->location ? $tour->location->fullName() : null;
            $defaultImage = "images/admin/a.png";
            return [

                'id' => $tour->id,
                'name' => $tour->admin ? $tour->admin->name : $tour->name, // Use admin name if available
                'location' => $locationName,
                'rating' => $tour->average_rating,
                'main_image' => $tour->admin ? $tour->admin->image ? asset('storage/' . $tour->admin->image) : asset('storage/' . $defaultImage) : $tour->main_image, // Use admin image if available
                'is_active' => $tour->is_active,

            ];
        });

        return $this->success('All tours retrieved successfully', [
            'tours' => $result,
        ]);
    }

    public function showTour($id)
    {
        $tourQuery = Tour::with([
            'images',
            'location.city.country',
            'admin',
        ]);

        $user = auth('sanctum')->user();

        if ($user && $user instanceof Admin) {
            $tourQuery->with('schedules');
        } else {
            $tourQuery->with([
                'schedules' => function ($query) {
                    $query->where('start_date', '>=', Carbon::now())
                        ->where('available_spots', '>', 0);
                }
            ]);
        }
        $tour = $tourQuery->where('id', $id)->first();

        if (!$tour) {
            return $this->error('Tour not found', 404);
        }

        $policies = Policy::where('service_type', 4)->get()->map(function ($policy) {
            return [
                'policy_type' => $policy->policy_type,
                'cutoff_time' => $policy->cutoff_time,
                'penalty_percentage' => $policy->penalty_percentage,
            ];
        });

        return $this->success('Tour retrieved successfully', [
            'tour' => new TourResource($tour),
            'policies' => $policies,
        ]);
    }

    public function bookTourByPoint($id, TourBookingRequest $request)
    {
        $tour = Tour::find($id);

        if (!$tour) {
            return $this->error('Tour not found', 404);
        }

        $schedule = $tour->schedules()->find($request->schedule_id);

        if (!$schedule || !$schedule->is_active) {
            return $this->error('Selected schedule not found or is not active for this tour.', 404);
        }

        $now = Carbon::now();
        $startDate = Carbon::parse($schedule->start_date);

        if ($now->greaterThanOrEqualTo($startDate)) {
            return $this->error('Booking must be made at least one day before the tour start date.', 400);
        }

        $existingBookings = TourBooking::where('tour_id', $tour->id)
            ->sum(DB::raw('number_of_adults + number_of_children'));

        $newBookingCount = $request->number_of_adults + ($request->number_of_children ?? 0);
        $totalAfterBooking = $existingBookings + $newBookingCount;

        if ($totalAfterBooking > $tour->max_capacity) {
            return $this->error('Cannot book: capacity exceeded.', 400);
        }

        $user = auth('sanctum')->user();
        $userRank = $user->rank ?? new UserRank(['user_id' => $user->id]);
        $userPoints = $userRank->points_earned ?? 0;

        $rule = DiscountPoint::where('action', 'book_tour')->first();

        if (!$rule || $userPoints < $rule->required_points) {
            return $this->error('You do not have enough reward points to book this tour. Minimum required: ' . ($rule->required_points ?? 'N/A'), 403);
        }

        $originalCost = $tour->base_price * $newBookingCount;
        $discountAmount = $originalCost * ($rule->discount_percentage / 100);
        $totalCost = $originalCost - $discountAmount;

        $bookingReference = 'TB-' . strtoupper(uniqid());
        $booking = Booking::create([
            'booking_reference' => $bookingReference,
            'user_id' => $user->id,
            'booking_type' => 1,
            'total_price' => $totalCost,
            'discount_amount' => $discountAmount,
            'payment_status' => 1,
            'booking_date' => now(),
            'status' => 'confirmed',
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'payment_reference' => 'PAY-' . strtoupper(uniqid()),
            'amount' => $totalCost,
            'payment_date' => now(),
            'payment_method' => 'points',
            'status' => 'completed',
        ]);

        $tourReservation = TourBooking::create([
            'user_id' => $user->id,
            'tour_id' => $tour->id,
            'schedule_id' => $schedule->id,
            'number_of_adults' => $request->number_of_adults,
            'number_of_children' => $request->number_of_children ?? 0,
            'booking_id' => $booking->id,
            'cost' => $totalCost,
        ]);

        $userRank->points_earned -= $rule->required_points;
        $userRank->save();

        $schedule->decrement('available_spots', $newBookingCount);

        return $this->success('Tour booked successfully with discount applied.', [
            'booking_reference' => $booking->booking_reference,
            'reservation_id' => $tourReservation->id,
            'tour' => $tourReservation->tour_id,
            'schedule' => $tourReservation->schedule_id,
            'cost' => $totalCost,
            'discount_applied' => true,
            'discount_amount' => $discountAmount,
        ]);
    }

    public function bookTour($id, TourBookingRequest $request)
    {
        $tour = Tour::find($id);

        if (!$tour) {
            return $this->error('Tour not found', 404);
        }

        $schedule = $tour->schedules()->where('is_active', true)->first();

        if (!$schedule) {
            return $this->error('No active schedule found for this tour.', 404);
        }

        $now = Carbon::now();
        $startDate = Carbon::parse($schedule->start_date);

        if ($now->greaterThanOrEqualTo($startDate)) {
            return $this->error('Booking must be made at least one day before the tour start date.', 400);
        }

        $existingBookings = TourBooking::where('tour_id', $tour->id)->sum(DB::raw('number_of_adults + number_of_children'));

        $newBookingCount = $request->number_of_adults + ($request->number_of_children ?? 0);
        $totalAfterBooking = $existingBookings + $newBookingCount;

        if ($totalAfterBooking > $tour->max_capacity) {
            return $this->error('Cannot book: capacity exceeded.', 400);
        }

        $bookingReference = 'TB-' . strtoupper(uniqid());
        $totalCost = $tour->base_price * $newBookingCount;

        $promotion = null;
        $promotionCode = $request->promotion_code;

        if ($promotionCode) {
            $promotion = Promotion::where('promotion_code', $promotionCode)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(function ($q) {
                    $q->where('applicable_type', 1)
                        ->orWhere('applicable_type', 2);
                })
                ->first();

            if (!$promotion || !$promotion->isActive) {
                return $this->error('Invalid or expired promotion code', 400);
            }

            if ($totalCost < $promotion->minimum_purchase) {
                return $this->error("Total must be at least {$promotion->minimum_purchase} to use this code.", 400);
            }

            if (!in_array($promotion->applicable_type, [null, 1, 2])) {
                return $this->error('This code cannot be applied to this tour booking', 400);
            }
        }

        $discountAmount = 0;
        if ($promotion) {
            $discountAmount = $promotion->discount_type == 1
                ? ($totalCost * $promotion->discount_value / 100)
                : $promotion->discount_value;

            $discountAmount = min($discountAmount, $totalCost);
        }

        $totalCostAfterDiscount = $totalCost - $discountAmount;

        $booking = Booking::create([
            'booking_reference' => $bookingReference,
            'user_id' => auth('sanctum')->id(),
            'booking_type' => 1,
            'total_price' => $totalCostAfterDiscount,
            'payment_status' => 1,
        ]);

        if (!$booking) {
            return $this->error('Failed to create booking', 500);
        }

        Payment::create([
            'booking_id' => $booking->id,
            'payment_reference' => 'PAY-' . strtoupper(uniqid()),
            'amount' => $totalCostAfterDiscount,
            'payment_date' => now(),
            'payment_method' => 'credit_card', // or get from request
            'status' => 'completed',
        ]);

        $tourReservation = TourBooking::create([
            'user_id' => auth('sanctum')->id(),
            'tour_id' => $tour->id,
            'schedule_id' => $schedule->id,
            'number_of_adults' => $request->number_of_adults,
            'number_of_children' => $request->number_of_children ?? 0,
            'booking_id' => $booking->id,
            'cost' => $totalCostAfterDiscount,
        ]);

        if ($promotion) {
            $promotion->increment('current_usage');
        }

        $this->addPointsFromAction(auth('sanctum')->user(), 'book_tour', $newBookingCount);

        $schedule->decrement('available_spots', $newBookingCount);

        return $this->success('Tour booked successfully', [
            'booking_reference' => $booking->booking_reference,
            'reservation_id' => $tourReservation->id,
            'tour' => $tourReservation->tour_id,
            'schedule' => $tourReservation->schedule_id,
            'cost' => $tourReservation->cost,
            'discount_amount' => $discountAmount,
        ]);
    }

}