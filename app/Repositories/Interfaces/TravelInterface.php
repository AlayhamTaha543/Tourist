<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\Travel\TravelBookingRequest;
use Illuminate\Http\Request;

interface TravelInterface
{
    public function getAllFlights();
    public function getFlight($id);
    public function getAvailableFlights();
    public function getAvailableFlightsDate(Request $request);
    public function getAgency($id);
    public function getAllAgency();

    public function bookFlight($id, TravelBookingRequest $request);
    public function bookFlightByPoint($id, TravelBookingRequest $request);
}
