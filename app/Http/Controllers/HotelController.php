<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelBookingRequest;
use App\Interface\HotelInterface;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    protected $hotelRepository;
    public function __construct(HotelInterface $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }
    public function showHotel($id){
        return $this->hotelRepository->showHotel($id);
    }
    public function showAllHotel(){
        return $this->hotelRepository->showAllHotel();
    }
    public function showNearByHotel(Request $request){
        return $this->hotelRepository->showNearByHotel($request);
    }
    public function showAviableRoom( $id){
        return $this->hotelRepository->showAviableRoom($id);
    }
    public function showAviableRoomType( $id,Request $request){
        return $this->hotelRepository->showAviableRoomType($id, $request);
    }
    public function bookHotel( $id,HotelBookingRequest $request){
        return $this->hotelRepository->bookHotel($id, $request);
    }
}
