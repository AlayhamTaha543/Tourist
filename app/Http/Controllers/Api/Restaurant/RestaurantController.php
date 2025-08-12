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
    protected $retaurantRepository;
    public function __construct(RestaurantInterface $retaurantRepository)
    {
        $this->retaurantRepository = $retaurantRepository;
    }

    public function showRestaurant($id)
    {
        return $this->retaurantRepository->showRestaurant($id);
    }
    public function showAllRestaurant()
    {
        $restaurants = Restaurant::with('location')->get();
        return response()->json([
            'restaurants' => ShowAllRestaurantsResource::collection($restaurants),
        ]);
    }
    public function showNearByRestaurant(Request $request)
    {
        return $this->retaurantRepository->showNearByRestaurant($request);
    }
    public function showRestaurantByLocation(Request $request)
    {
        return $this->retaurantRepository->showRestaurantByLocation($request);
    }

    public function showMenuCategory()
    {
        return $this->retaurantRepository->showMenuCategory();
    }
    public function showMenuItem($id)
    {
        return $this->retaurantRepository->showMenuItem($id);
    }
    public function showAviableTable($id)
    {
        return $this->retaurantRepository->showAviableTable($id);
    }
    public function bookTable($id, RestaurantBookingRequest $request)
    {
        return $this->retaurantRepository->bookTable($id, $request);
    }
}
