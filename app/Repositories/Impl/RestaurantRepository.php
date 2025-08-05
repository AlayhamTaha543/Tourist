<?php

namespace App\Repositories\Impl;

use App\Http\Requests\Restaurants\RestaurantBookingRequest;
use App\Models\Booking;
use App\Models\Favourite;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Policy;
use App\Models\Promotion;
use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use App\Models\RestaurantTable;
use App\Repositories\Interfaces\RestaurantInterface;
use App\Traits\HandlesUserPoints;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class RestaurantRepository implements RestaurantInterface
{
    use ApiResponse,HandlesUserPoints;

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
            return $this->success('Store retrieved successfully', [
                'restaurant ' => $restaurant,
                'category'=>$result,
                'is_favourited' => $isFavourited,
                'promotion' => $promotion ? [
                    'promotion_code' => $promotion->promotion_code,
                    'description' => $promotion->description,
                    'discount_type' => $promotion->discount_type,
                    'discount_value' => $promotion->discount_value,
                    'minimum_purchase' => $promotion->minimum_purchase,
                ] : null,
                'policies' => $policies,
            ]);
    }
    public function showAllRestaurant(){
        $restaurants = Restaurant::with('menuCategories')->get();
        $result = $restaurants->map(function($restaurant) {
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
            return [
                'restaurant' => $restaurant,
                'categories' => $restaurant->menuCategories,
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
        return $this->success('All restaurants retrieved successfully', [
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
    public function showMenuCategory(){
        $categories = MenuCategory::with('restaurant','menuItems')->get();
        if (!$categories) {
            return $this->error('No categories found', 404);
        }

        return $this->success('categories retrieved successfully', [
            'categories' => $categories,
        ]);
    }
    public function showMenuItem($id){
        $category = MenuCategory::find($id);
        if ( !$category) {
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
    public function showAviableTable($id){
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }

        $availableTablesIndoor = RestaurantTable::where('restaurant_id', $restaurant->id)
        ->where('number', '>', 0)
        ->where('location',  'Indoor')
        ->get();
        $availableTablesOutdoor = RestaurantTable::where('restaurant_id', $restaurant->id)
        ->where('number', '>', 0)
        ->where('location',  'Outdoor')
        ->get();
        $availableTablesPrivate = RestaurantTable::where('restaurant_id', $restaurant->id)
        ->where('number', '>', 0)
        ->where('location',  'Private')
        ->get();

        return $this->success('Available tables retrieved successfully', [
            'available_tables_Inside' => $availableTablesIndoor,
            'available_tables_outdoor' => $availableTablesOutdoor,
            'available_tables_private' => $availableTablesPrivate,
        ]);
    }
}
