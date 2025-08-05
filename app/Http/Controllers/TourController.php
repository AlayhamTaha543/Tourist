<?php

namespace App\Http\Controllers;

use App\Http\Requests\TourBookingRequest;
use App\Interface\TourInterface;
use Illuminate\Http\Request;

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
