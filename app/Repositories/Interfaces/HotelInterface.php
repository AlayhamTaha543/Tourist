<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\Hotel\HotelBookingRequest;
use Illuminate\Http\Request;


interface HotelInterface
{
    public function showHotel($id);
    public function showAllHotel();
    public function showNearByHotel(Request $request);
    public function showAviableRoom($id);
    public function showAviableRoomType($id,Request $request);
    public function bookHotel($id,HotelBookingRequest $request);
}
