<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\Restaurants\RestaurantBookingRequest;
use Illuminate\Http\Request;

interface RestaurantInterface
{
    public function showRestaurant($id);
    public function showAllRestaurant();
    public function showRestaurantByLocation(Request $request);
    public function showNearByRestaurant(Request $request);
    public function showMenuCategory();
    public function showMenuItem($id);
    public function bookTable($id,RestaurantBookingRequest $request);
    public function showAviableTable($id);
}
