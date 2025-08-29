<?php

namespace App\Http\Controllers\Api\Travel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Travel\TravelBookingRequest;
use App\Repositories\Interfaces\TravelInterface;
use App\Repositories\Interfaces\TourInterface;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    protected $travelRepository;
    protected $tourRepository;

    public function __construct(TravelInterface $travelRepository, TourInterface $tourRepository)
    {
        $this->travelRepository = $travelRepository;
        $this->tourRepository = $tourRepository;
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
    public function bookFlight($id, TravelBookingRequest $request)
    {
        $bookingResult = $this->travelRepository->bookFlight($id, $request);

        if ($request->filled('number_of_adults') && $request->filled('schedule_id')) {
            $tourBookingRequest = new \App\Http\Requests\Tour\TourBookingRequest();
            $tourBookingRequest->merge([
                'number_of_adults' => $request->number_of_adults,
                'schedule_id' => $request->schedule_id,
            ]);

            $tourId = \App\Models\TourSchedule::find($request->schedule_id)->tour_id;
            $this->tourRepository->bookTour($tourId, $tourBookingRequest);
        }

        return $bookingResult;
    }
    public function bookFlightByPoint($id, TravelBookingRequest $request)
    {
        return $this->travelRepository->bookFlightByPoint($id, $request);
    }
    public function getAllBookedFlights()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $id = $user->id;
        $bookedFlights = $this->travelRepository->getAllBookedFlights($id);

        return response()->json([
            'success' => true,
            'data' => $bookedFlights
        ]);
    }
    public function getNearestBookedFlight()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $id = $user->id;
        $bookedFlights = $this->travelRepository->getNearestBookedFlight($id);

        return response()->json([
            'success' => true,
            'data' => $bookedFlights
        ]);
    }
    public function searchFlights(Request $request)
    {
        return $this->travelRepository->searchFlights($request);
    }
}
