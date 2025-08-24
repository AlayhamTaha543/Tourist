<?php

namespace App\Repositories\Impl;

use App\Helper\CountryOfNextTrip;
use App\Http\Requests\Restaurant\RestaurantBookingRequest;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\ShowAllRestaurantsResource;
use App\Models\Booking;
use App\Models\Favourite;
use App\Models\MenuCategory;
use App\Models\Payment;
use App\Models\MenuItem;
use App\Models\Policy;
use App\Models\Promotion;
use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use App\Models\User;
use App\Models\RestaurantChair;
use App\Models\ChairAvailability;
use App\Repositories\Interfaces\RestaurantInterface;
use App\Traits\HandlesUserPoints;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class RestaurantRepository implements RestaurantInterface
{
    use ApiResponse, HandlesUserPoints;

    public function showRestaurant($id)
    {
        $restaurant = Restaurant::with('images', 'menuCategories.menuItems')
            ->where('id', $id)
            ->first();

        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }
        $user = auth()->user();

        $isFavourited = false;
        if ($user) {
            $isFavourited = Favourite::where([
                'user_id' => $user->id,
                'favoritable_id' => $restaurant->id,
                'favoritable_type' => Restaurant::class,
            ])->exists();
        }
        $now = now();
        $promotion = Promotion::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('applicable_type', 5)
            ->first();
        $result = [];
        foreach ($restaurant->menuCategories as $menuCategorie) {
            $restaurantData = [
                'category ' => $menuCategorie->name,
            ];

            foreach ($menuCategorie->menuItems as $menuItem) {
                if ($menuItem->restaurant_id == $restaurant->id) {
                    $menuItemData = [
                        'menuItem' => $menuItem->name,
                    ];
                    $restaurantData['menuItems'][] = $menuItemData;
                }
            }
            $result[] = $restaurantData;
        }
        $policies = Policy::where('service_type', 3)->get()->map(function ($policy) {
            return [
                'policy_type' => $policy->policy_type,
                'cutoff_time' => $policy->cutoff_time,
                'penalty_percentage' => $policy->penalty_percentage,
            ];
        });
        return new RestaurantResource($restaurant);
    }
    public function showAllRestaurant(bool $nextTrip = false, ?User $user = null)
    {
        // Get the user if not provided
        if (!$user) {
            $user = auth()->user();
        }

        $countryName = null;
        if ($nextTrip) {
            // Get country for next trip
            $countryName = CountryOfNextTrip::getCountryForUser($user->id);
        } else {
            // Get country from user's current location
            $userLocation = $user->location;
            if ($userLocation) {
                // Extract country name from location string
                $locationParts = array_map('trim', explode(',', $userLocation));
                if (count($locationParts) >= 2) {
                    $countryName = end($locationParts);
                } else {
                    $countryName = $locationParts[0];
                }
            }
        }

        // Build query with location filtering
        $restaurantsQuery = Restaurant::with(['menuCategories', 'location.city.country']);

        // Filter by country if provided
        if ($countryName) {
            $restaurantsQuery->whereHas('location.city.country', function ($query) use ($countryName) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($countryName) . '%']);
            });
        }

        $restaurants = $restaurantsQuery->get();

        // If no restaurants found and location was provided
        if ($restaurants->isEmpty() && $countryName) {
            return $this->success("No restaurants found in {$countryName}", [
                'restaurants' => [],
            ]);
        }

        // Transform restaurants using the resource and add additional data
        $result = $restaurants->map(function ($restaurant) use ($user) {
            $restaurantData = new ShowAllRestaurantsResource($restaurant);

            // Check if restaurant is favourited
            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $restaurant->id,
                    'favoritable_type' => Restaurant::class,
                ])->exists();
            }

            // Get active promotion
            $now = now();
            $promotion = Promotion::where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('applicable_type', 5)
                ->first();

            return $restaurantData->toArray(request());
        });

        return $this->success('Restaurants retrieved successfully', [
            'restaurants' => $result,
        ]);
    }
    public function showRestaurantByLocation(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius', 5);

        $nearbyRestaurants = Restaurant::selectRaw(
            "id, restaurant_name, ST_Distance_Sphere(location, POINT(?, ?)) as distance",
            [$longitude, $latitude]
        )
            ->having('distance', '<=', $radius * 1000)
            ->get();

        return $this->success('Nearby restaurants retrieved successfully', [
            'restaurants' => $nearbyRestaurants,
        ]);
    }
    public function showNearByRestaurant(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius', 5);

        $nearbyRestaurants = Restaurant::selectRaw(
            "id, restaurant_name, ST_Distance_Sphere(location, POINT(?, ?)) as distance",
            [$longitude, $latitude]
        )
            ->having('distance', '<=', $radius * 1000)
            ->get();

        return $this->success('Nearby restaurants retrieved successfully', [
            'restaurants' => $nearbyRestaurants,
        ]);
    }
    public function showMenuCategory()
    {
        $categories = MenuCategory::with('restaurant', 'menuItems')->get();
        if (!$categories) {
            return $this->error('No categories found', 404);
        }

        return $this->success('categories retrieved successfully', [
            'categories' => $categories,
        ]);
    }
    public function showMenuItem($id)
    {
        $category = MenuCategory::find($id);
        if (!$category) {
            return $this->error('Menu item not found', 404);
        }
        $menuItem = MenuItem::where('category_id', $category->id)->first();

        if (!$menuItem || !$category) {
            return $this->error('Menu item not found', 404);
        }

        return $this->success('Menu item retrieved successfully', [
            'menu_item' => $menuItem,
        ]);
    }
    public function bookChairs($id, RestaurantBookingRequest $request)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }

        $reservationDate = $request->reservation_date;
        $reservationTime = Carbon::parse($request->reservation_time);
        $durationTime = $request->duration_time ?? 1; // Default to 1 hour if not provided
        $location = $request->location;
        $numberOfGuests = $request->number_of_guests;

        $restaurantChair = RestaurantChair::where('restaurant_id', $restaurant->id)
            ->where('location', $location)
            ->first();

        if (!$restaurantChair) {
            return $this->error("No chairs found for location {$location} at this restaurant", 404);
        }

        // Check availability for all hours of the duration first
        $availabilityRecordsToUpdate = [];
        for ($i = 0; $i < $durationTime; $i++) {
            $currentTimeSlot = $reservationTime->copy()->addHours($i)->format('H:i:s');

            $chairAvailability = ChairAvailability::where('restaurant_chair_id', $restaurantChair->id)
                ->where('date', $reservationDate)
                ->where('time_slot', $currentTimeSlot)
                ->first();

            if (!$chairAvailability || $chairAvailability->available_chairs_count < $numberOfGuests) {
                return $this->error('Not enough chairs available for the selected date, time, and location for the entire duration', 400);
            }
            $availabilityRecordsToUpdate[] = $chairAvailability;
        }

        // If all checks passed, decrement the available chairs count for each hour
        foreach ($availabilityRecordsToUpdate as $chairAvailability) {
            $chairAvailability->decrement('available_chairs_count', $numberOfGuests);
        }

        $bookingReference = 'RB-' . strtoupper(uniqid());
        $basePrice = $restaurantChair->cost * $numberOfGuests * $durationTime; // Cost per chair per hour

        $promotion = null;
        $promotionCode = $request->promotion_code;

        if ($promotionCode) {
            $promotion = Promotion::where('promotion_code', $promotionCode)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(function ($q) {
                    $q->where('applicable_type', 1)
                        ->orWhere('applicable_type', 5);
                })
                ->first();

            if (!$promotion || !$promotion->is_active) {
                return $this->error('Invalid or expired promotion code', 400);
            }

            if ($basePrice < $promotion->minimum_purchase) {
                return $this->error("Total must be at least {$promotion->minimum_purchase} to use this code.", 400);
            }

            if (!in_array($promotion->applicable_type, [null, 1, 5])) {
                return $this->error('This code cannot be applied to this restaurant booking', 400);
            }
        }

        $discountAmount = 0;
        if ($promotion) {
            $discountAmount = $promotion->discount_type == 1
                ? ($basePrice * $promotion->discount_value / 100)
                : $promotion->discount_value;

            $discountAmount = min($discountAmount, $basePrice);
        }

        $totalPriceAfterDiscount = $basePrice - $discountAmount;

        $booking = Booking::create([
            'booking_reference' => $bookingReference,
            'user_id' => auth('sanctum')->id(),
            'booking_type' => 4,
            'total_price' => $totalPriceAfterDiscount,
            'discount_amount' => $discountAmount,
            'payment_status' => 1,
        ]);

        if (!$booking) {
            return $this->error('Failed to create booking', 500);
        }

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $totalPriceAfterDiscount,
            'payment_date' => now(),
            'payment_method' => 'credit_card', // or get from request
            'status' => 'completed',
        ]);

        $tableReservation = RestaurantBooking::create([
            'booking_id' => $booking->id,
            'user_id' => auth('sanctum')->id(),
            'restaurant_id' => $restaurant->id,
            'restaurant_chair_id' => $restaurantChair->id,
            'reservation_date' => $reservationDate,
            'reservation_time' => $reservationTime->format('H:i:s'),
            'number_of_guests' => $numberOfGuests,
            'cost' => $totalPriceAfterDiscount,
            'duration_time' => $durationTime, // New: save duration time
        ]);

        if ($promotion) {
            $promotion->increment('current_usage');
        }

        $this->addPointsFromAction(auth('sanctum')->user(), 'book_restaurant', 1);

        return $this->success('Chairs reserved successfully', [
            'reservation_id' => $tableReservation->id,
            'date' => $tableReservation->reservation_date,
            'time' => $tableReservation->reservation_time,
            'duration_time' => $tableReservation->duration_time, // New: return duration time
            'location' => $location,
            'number_of_guests' => $tableReservation->number_of_guests,
            'cost' => $tableReservation->cost,
            'booking_reference' => $booking->booking_reference,
            'discount_amount' => $discountAmount,
        ]);
    }

    public function showAviableChairs($id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }

        $availabilityByLocation = [];
        $chairTypes = RestaurantChair::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->where('is_reservable', true)
            ->get();

        // Generate time slots for the restaurant
        $timeSlots = $this->generateTimeSlots($restaurant);

        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            $currentDate = Carbon::now()->addDays($dayOffset);
            $dateString = $currentDate->toDateString();
            $dayName = $currentDate->format('l');

            foreach ($chairTypes as $chairType) {
                if (!isset($availabilityByLocation[$chairType->location])) {
                    $availabilityByLocation[$chairType->location] = [];
                }

                $dayData = [
                    'date' => $dateString,
                    'day_name' => $dayName,
                    'time_slots' => []
                ];

                foreach ($timeSlots as $timeSlot) {
                    $availableCount = ChairAvailability::where('restaurant_chair_id', $chairType->id)
                        ->where('date', $dateString)
                        ->where('time_slot', $timeSlot)
                        ->first();

                    $availableChairs = $availableCount ? $availableCount->available_chairs_count : 0;

                    $dayData['time_slots'][] = [
                        'time' => Carbon::parse($timeSlot)->format('H:i'),
                        'available_chairs' => $availableChairs,
                        'total_chairs_for_slot' => $chairType->total_chairs,
                        'cost_per_chair' => $chairType->cost,
                    ];
                }
                $availabilityByLocation[$chairType->location][] = $dayData;
            }
        }

        return $this->success('Available chairs retrieved successfully', [
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'chair_availability_by_location' => $availabilityByLocation,
        ]);
    }

    /**
     * Generate time slots for a restaurant
     */
    private function generateTimeSlots($restaurant)
    {
        $timeSlots = [];

        if (!$restaurant->opening_time || !$restaurant->closing_time) {
            // Default time slots if not specified
            return ['12:00:00', '13:00:00', '14:00:00', '18:00:00', '19:00:00', '20:00:00', '21:00:00'];
        }

        $openingTime = Carbon::parse($restaurant->opening_time);
        $closingTime = Carbon::parse($restaurant->closing_time);

        $current = clone $openingTime;

        while ($current < $closingTime) {
            $timeSlots[] = $current->format('H:i:s');
            $current->addHour(); // 1-hour intervals
        }

        return $timeSlots;
    }
}