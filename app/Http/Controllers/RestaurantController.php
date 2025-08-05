<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantBookingRequest;
use App\Http\Requests\RestaurantOrderRequest;
use App\Interface\RestaurantInterface;
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
        return $this->retaurantRepository->showAllRestaurant();
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
    public function bookTable($id,RestaurantBookingRequest $request)
    {
        return $this->retaurantRepository->bookTable($id,$request);
    }
}
