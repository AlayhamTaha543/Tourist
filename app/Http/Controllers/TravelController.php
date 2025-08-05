<?php

namespace App\Http\Controllers;

use App\Http\Requests\TravelBookingRequest;
use App\Interface\TravelInterface;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    protected $travelRepository;
    public function __construct(TravelInterface $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }
    public function getAllFlights()
    {
        return $this->travelRepository->getAllFlights();
    }
    public function getFlight($id)
    {
        return $this->travelRepository->getFlight($id);
    }
    public function getAvailableFlights()
    {
        return $this->travelRepository->getAvailableFlights();
    }
    public function getAvailableFlightsDate(Request $request)
    {
        return $this->travelRepository->getAvailableFlightsDate($request);
    }
    public function getAgency($id)
    {
        return $this->travelRepository->getAgency($id);
    }
    public function getAllAgency()
    {
        return $this->travelRepository->getAllAgency();
    }
    public function bookFlight($id,TravelBookingRequest $request)
    {
        return $this->travelRepository->bookFlight($id,$request);
    }
    public function bookFlightByPoint($id,TravelBookingRequest $request)
    {
        return $this->travelRepository->bookFlightByPoint($id,$request);
    }
   
}
