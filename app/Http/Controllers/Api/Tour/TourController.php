<?php

namespace App\Http\Controllers\Api\Tour;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tour\TourBookingRequest;
use App\Repositories\Interfaces\TourInterface;


class TourController extends Controller
{
    protected $tourRepository;
    public function __construct(TourInterface $tourRepository)
    {
        $this->tourRepository = $tourRepository;
    }
    public function showAllTour()
    {
        return $this->tourRepository->showAllTour();
    }
    public function showTour($id)
    {
        return $this->tourRepository->showTour($id);
    }
    public function bookTour($id,TourBookingRequest $request)
    {
        return $this->tourRepository->bookTour($id,$request);
    }
    public function bookTourByPoint($id,TourBookingRequest $request)
    {
        return $this->tourRepository->bookTourByPoint($id,$request);
    }
}
