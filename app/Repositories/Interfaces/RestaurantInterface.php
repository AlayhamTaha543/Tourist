<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\Restaurant\RestaurantBookingRequest;
use App\Models\User;
use Illuminate\Http\Request;

interface RestaurantInterface
{
    public function showRestaurant($id);
    public function showAllRestaurant(bool $nextTrip = false, ?User $user = null);
    public function showRestaurantByLocation(Request $request);
    public function showNearByRestaurant(Request $request);
    public function showMenuCategory();
    public function showMenuItem($id);
    public function bookChairs($id, RestaurantBookingRequest $request);
    public function showAviableChairs($id);
}
