<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Auth\PayRequest;
use App\Repositories\Interfaces\BookingInterface;
use Illuminate\Support\Facades\Request;

class BookingController extends Controller
{
    protected $bookingRepository;
    public function __construct(BookingInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }
    public function payForBooking($id, PayRequest $request)
    {
        return $this->bookingRepository->payForBooking($id, $request);
    }
    public function getBookingHistory()
    {
        return $this->bookingRepository->getBookingHistory();
    }
    public function getAllBookings()
    {
        return $this->bookingRepository->getAllBookings();
    }
    public function cancelBooking($id)
    {
        return $this->bookingRepository->cancelBooking($id);
    }
    public function modifyBooking(Request $request, $id)
    {
        return $this->bookingRepository->modifyBooking($request, $id);
    }
}
