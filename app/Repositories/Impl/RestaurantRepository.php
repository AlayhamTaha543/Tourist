<?php

namespace App\Repositories\Impl;

use App\Helper\CountryOfNextTrip;
use App\Http\Requests\Restaurant\RestaurantBookingRequest;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\ShowAllRestaurantsResource;
use App\Models\Booking;
use App\Models\Favourite;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Policy;
use App\Models\Promotion;
use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use App\Models\RestaurantTable;
use App\Models\TableAvailability;
use App\Models\User;
use App\Repositories\Interfaces\RestaurantInterface;
use App\Traits\HandlesUserPoints;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

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
        // return $this->success('Store retrieved successfully', [
        //     'restaurant ' => $restaurant,
        //     'category' => $result,
        //     'is_favourited' => $isFavourited,
        //     'promotion' => $promotion ? [
        //         'promotion_code' => $promotion->promotion_code,
        //         'description' => $promotion->description,
        //         'discount_type' => $promotion->discount_type,
        //         'discount_value' => $promotion->discount_value,
        //         'minimum_purchase' => $promotion->minimum_purchase,
        //     ] : null,
        //     'policies' => $policies,
        // ]);
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
        /**
     * Get restaurant availability for next 7 days
     */
    public function getRestaurantAvailability($id)
    {
        $restaurant = Restaurant::with(['tables' => function($query) {
            $query->where('is_active', true)->where('is_reservable', true);
        }])->find($id);

        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }

        $availability = $this->generateSevenDayAvailability($restaurant);

        return $this->success('Restaurant availability retrieved successfully', [
            'restaurant' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'location' => $restaurant->location ? $restaurant->location->fullName() : null,
                'opening_time' => $restaurant->opening_time ? Carbon::parse($restaurant->opening_time)->format('H:i') : null,
                'closing_time' => $restaurant->closing_time ? Carbon::parse($restaurant->closing_time)->format('H:i') : null,
            ],
            'availability' => $availability
        ]);
    }

    /**
     * Get available tables for a specific date and time
     */
    public function getAvailableTablesForDateTime($restaurantId, Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today|before_or_equal:' . Carbon::now()->addDays(6)->toDateString(),
            'time' => 'required|date_format:H:i'
        ]);

        $restaurant = Restaurant::find($restaurantId);
        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }

        $availableTables = $this->getAvailableTablesForSlot($restaurantId, $request->date, $request->time);

        if ($availableTables->isEmpty()) {
            return $this->error('No tables available for the selected date and time', 404);
        }

        $groupedTables = $availableTables->groupBy('location');
        $result = [];

        foreach ($groupedTables as $location => $tables) {
            $result[] = [
                'location' => $location,
                'available_tables' => $tables->count(),
                'tables' => $tables->map(function($table) {
                    return [
                        'id' => $table->id,
                        'number' => $table->number,
                        'cost' => $table->cost
                    ];
                })
            ];
        }

        return $this->success('Available tables retrieved successfully', [
            'date' => $request->date,
            'time' => $request->time,
            'available_by_location' => $result
        ]);
    }
     /**
     * Book a table with the new availability system
     */
    public function bookTableWithAvailability($id, RestaurantBookingRequest $request)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }

        $reservationDate = $request->reservation_date;
        $reservationTime = $request->reservation_time;
        $tableId = $request->table_id;

        // Check if the specific table is available
        $isTableAvailable = $this->isTableAvailable($tableId, $reservationDate, $reservationTime);
        
        if (!$isTableAvailable) {
            return $this->error('Selected table is not available for this date and time', 400);
        }

        // Your existing booking logic...
        $bookingReference = 'RB-' . strtoupper(uniqid());
        $table = RestaurantTable::find($tableId);
        $basePrice = $table->cost;

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

        $tableReservation = RestaurantBooking::create([
            'booking_id' => $booking->id,
            'user_id' => auth('sanctum')->id(),
            'restaurant_id' => $restaurant->id,
            'table_id' => $tableId,
            'reservation_date' => $reservationDate,
            'reservation_time' => $reservationTime,
            'number_of_guests' => $request->number_of_guests,
            'cost' => $totalPriceAfterDiscount,
        ]);

        // Block the table for this time slot
        $this->blockTableForSlot($tableId, $reservationDate, $reservationTime);

        if ($promotion) {
            $promotion->increment('current_usage');
        }

        $this->addPointsFromAction(auth('sanctum')->user(), 'book_restaurant', 1);

        return $this->success('Table reserved successfully', [
            'reservation_id' => $tableReservation->id,
            'date' => $tableReservation->reservation_date,
            'time' => $tableReservation->reservation_time,
            'table_id' => $tableReservation->table_id,
            'cost' => $tableReservation->cost,
            'booking_reference' => $booking->booking_reference,
            'discount_amount' => $discountAmount,
        ]);
    }

    /**
     * Initialize table availability for a restaurant
     */
    public function initializeTableAvailability($restaurantId, $days = 7)
    {
        $restaurant = Restaurant::with('tables')->find($restaurantId);
        
        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }

        $tables = $restaurant->tables()->where('is_active', true)->where('is_reservable', true)->get();
        
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->addDays($i)->toDateString();
            
            foreach ($tables as $table) {
                $timeSlots = $this->generateTimeSlots($restaurant);
                
                foreach ($timeSlots as $timeSlot) {
                    TableAvailability::firstOrCreate([
                        'table_id' => $table->id,
                        'date' => $date,
                        'time_slot' => $timeSlot
                    ], [
                        'is_available' => true,
                        'is_blocked' => false,
                        'price_multiplier' => 1.0
                    ]);
                }
            }
        }

        return $this->success('Table availability initialized successfully');
    }

    // Private helper methods

    /**
     * Generate 7-day availability data
     */
    private function generateSevenDayAvailability($restaurant)
    {
        $availability = [];
        
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i);
            $dateString = $date->toDateString();
            
            $dayAvailability = [
                'date' => $dateString,
                'day_name' => $date->format('l'),
                'time_slots' => []
            ];
            
            $timeSlots = $this->generateTimeSlots($restaurant);
            
            foreach ($timeSlots as $timeSlot) {
                $availableTables = $this->getAvailableTablesForSlot($restaurant->id, $dateString, $timeSlot);
                
                if ($availableTables->count() > 0) {
                    $groupedTables = $availableTables->groupBy('location');
                    $locationData = [];
                    
                    foreach ($groupedTables as $location => $tables) {
                        $locationData[] = [
                            'location' => $location,
                            'available_tables' => $tables->count()
                        ];
                    }
                    
                    $dayAvailability['time_slots'][] = [
                        'time' => Carbon::parse($timeSlot)->format('H:i'),
                        'total_available_tables' => $availableTables->count(),
                        'locations' => $locationData
                    ];
                }
            }
            
            // Only include days that have available time slots
            if (!empty($dayAvailability['time_slots'])) {
                $availability[] = $dayAvailability;
            }
        }
        
        return $availability;
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

    /**
     * Get available tables for a specific date and time slot
     */
    private function getAvailableTablesForSlot($restaurantId, $date, $timeSlot)
    {
        $timeSlotFormatted = Carbon::parse($timeSlot)->format('H:i:s');
        
        return RestaurantTable::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->where('is_reservable', true)
            ->whereDoesntHave('availability', function ($query) use ($date, $timeSlotFormatted) {
                $query->where('date', $date)
                      ->where('time_slot', $timeSlotFormatted)
                      ->where(function ($q) {
                          $q->where('is_available', false)
                            ->orWhere('is_blocked', true);
                      });
            })
            ->get();
    }

    /**
     * Check if a table is available for a specific date and time
     */
    private function isTableAvailable($tableId, $date, $time)
    {
        $timeFormatted = Carbon::parse($time)->format('H:i:s');
        
        $availability = TableAvailability::where('table_id', $tableId)
            ->where('date', $date)
            ->where('time_slot', $timeFormatted)
            ->first();

        if (!$availability) {
            return true; // Available if no record exists
        }

        return $availability->is_available && !$availability->is_blocked;
    }

    /**
     * Block a table for a specific time slot
     */
    private function blockTableForSlot($tableId, $date, $time)
    {
        $timeFormatted = Carbon::parse($time)->format('H:i:s');
        
        TableAvailability::updateOrCreate(
            [
                'table_id' => $tableId,
                'date' => $date,
                'time_slot' => $timeFormatted
            ],
            [
                'is_available' => false,
                'is_blocked' => true
            ]
        );
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
    public function bookTable($id, RestaurantBookingRequest $request)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }

        $reservationDate = $request->reservation_date;
        $reservationTime = $request->reservation_time;

        $maxTables = $restaurant->max_tables;
        $countReservations = RestaurantBooking::where('restaurant_id', $restaurant->id)
            ->where('reservation_date', $reservationDate)
            ->count();

        if ($countReservations >= $maxTables) {
            return $this->error('No tables available for this date', 400);
        }

        $bookingReference = 'RB-' . strtoupper(uniqid());
        $basePrice = $restaurant->cost;

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

        $tableReservation = RestaurantBooking::create([
            'booking_id' => $booking->id,
            'user_id' => auth('sanctum')->id(),
            'restaurant_id' => $restaurant->id,
            'table_id' => rand(1, $restaurant->max_tables),
            'reservation_date' => $reservationDate,
            'reservation_time' => $reservationTime,
            'number_of_guests' => $request->number_of_guests,
            'cost' => $totalPriceAfterDiscount,
        ]);

        if ($promotion) {
            $promotion->increment('current_usage');
        }

        $this->addPointsFromAction(auth('sanctum')->user(), 'book_restaurant', 1);

        return $this->success('Table reserved successfully', [
            'reservation_id' => $tableReservation->id,
            'date' => $tableReservation->reservation_date,
            'time' => $tableReservation->reservation_time,
            'table_id' => $tableReservation->table_id,
            'cost' => $tableReservation->cost,
            'booking_reference' => $booking->booking_reference,
            'discount_amount' => $discountAmount,
        ]);
    }
    public function showAviableTable($id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }

        $availableTablesIndoor = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where('number', '>', 0)
            ->where('location', 'Indoor')
            ->get();
        $availableTablesOutdoor = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where('number', '>', 0)
            ->where('location', 'Outdoor')
            ->get();
        $availableTablesPrivate = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where('number', '>', 0)
            ->where('location', 'Private')
            ->get();

        return $this->success('Available tables retrieved successfully', [
            'available_tables_Inside' => $availableTablesIndoor,
            'available_tables_outdoor' => $availableTablesOutdoor,
            'available_tables_private' => $availableTablesPrivate,
        ]);
    }
}
