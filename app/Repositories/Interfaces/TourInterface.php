<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\Tour\TourBookingRequest;


interface TourInterface
{
    public function showAllTour();
    public function showTour($id);
    public function bookTour($id,TourBookingRequest $request);

    public function bookTourByPoint($id,TourBookingRequest $request);
}
