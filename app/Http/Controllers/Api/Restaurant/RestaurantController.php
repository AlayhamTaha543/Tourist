<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurants\RestaurantBookingRequest;
use App\Http\Resources\ShowAllRestaurantsResource;
use App\Models\Restaurant;
use App\Repositories\Interfaces\RestaurantInterface;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    protected $restaurantRepository;
    public function __construct(RestaurantInterface $restaurantRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
    }

    public function showRestaurant($id)
    {
        return $this->restaurantRepository->showRestaurant($id);
    }


    public function showNextTripRestaurant()
    {
        $user = auth()->user();
        return $this->restaurantRepository->showAllRestaurant(true, $user);
    }
    public function showAllRestaurant()
    {
        $user = auth()->user();
        return $this->restaurantRepository->showAllRestaurant(false, $user);
        // $restaurants = Restaurant::with('location')->get();
        // return response()->json([
        //     'restaurants' => ShowAllRestaurantsResource::collection($restaurants),
        // ]);
    }
    public function showNearByRestaurant(Request $request)
    {
        return $this->restaurantRepository->showNearByRestaurant($request);
    }
    public function showRestaurantByLocation(Request $request)
    {
        return $this->restaurantRepository->showRestaurantByLocation($request);
    }

    public function showMenuCategory()
    {
        return $this->restaurantRepository->showMenuCategory();
    }
    public function showMenuItem($id)
    {
        return $this->restaurantRepository->showMenuItem($id);
    }
    public function showAviableTable($id)
    {
        return $this->restaurantRepository->showAviableTable($id);
    }
    public function bookTable($id, RestaurantBookingRequest $request)
    {
        return $this->restaurantRepository->bookTable($id, $request);
    }
}
