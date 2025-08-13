<?php

namespace App\Http\Controllers\Api\Hotel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\HotelBookingRequest;
use App\Http\Requests\Hotel\HotelRequest;
use App\Repositories\Interfaces\HotelInterface;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    protected $hotelRepository;
    public function __construct(HotelInterface $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }
    public function showHotel($id)
    {
        return $this->hotelRepository->showHotel($id);
    }
    public function showAllHotel()
    {
        $user = auth()->user();
        return $this->hotelRepository->showAllHotel(false, $user);
    }
    public function showNextTripHotel()
    {
        $user = auth()->user();

        return $this->hotelRepository->showAllHotel(true, $user);
    }
    public function showNearByHotel(Request $request)
    {
        return $this->hotelRepository->showNearByHotel($request);
    }
    public function showAviableRoom($id)
    {
        return $this->hotelRepository->showAviableRoom($id);
    }
    public function showAviableRoomType($id, Request $request)
    {
        return $this->hotelRepository->showAviableRoomType($id, $request);
    }
    public function bookHotel($id, HotelBookingRequest $request)
    {
        return $this->hotelRepository->bookHotel($id, $request);
    }
}
