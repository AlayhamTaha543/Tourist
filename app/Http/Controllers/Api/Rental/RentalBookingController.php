<?php

namespace App\Http\Controllers\Api\Rental;

use App\Enum\RentalBookingStatus;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Rental\StoreRentalBookingRequest;
use App\Http\Requests\Rental\UpdateRentalBookingRequest;
use App\Services\Rental\RentalBookingService;
use Illuminate\Http\JsonResponse;

class RentalBookingController extends BaseController
{
    protected $bookingService;

    public function __construct(RentalBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(): JsonResponse
    {
        try {
            $bookings = $this->bookingService->getPaginatedBookings();
            return $this->successResponse($bookings);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreRentalBookingRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $result = $this->bookingService->createBooking($validated);
            return $this->successResponse($result, 'Booking created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Booking creation failed: ' . $e->getMessage(), 422);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $booking = $this->bookingService->getBookingById($id);

            if (!$booking) {
                return $this->resourceNotFound('Booking');
            }

            return $this->successResponse($booking);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateRentalBookingRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (!$this->bookingService->updateBooking($id, $validated)) {
                return $this->resourceNotFound('Booking');
            }

            $updatedBooking = $this->bookingService->getBookingById($id);
            return $this->successResponse($updatedBooking, 'Booking updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            if (!$this->bookingService->cancelBooking($id)) {
                return $this->resourceNotFound('Booking');
            }

            return $this->successResponse(null, 'Booking cancelled successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function startBooking(int $id): JsonResponse
    {
        try {
            if (!$this->bookingService->startBooking($id)) {
                return $this->errorResponse('Failed to start booking', 400);
            }

            $booking = $this->bookingService->getBookingById($id);
            return $this->successResponse($booking, 'Booking marked as active');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function completeBooking(int $id): JsonResponse
    {
        try {
            if (!$this->bookingService->completeBooking($id)) {
                return $this->errorResponse('Failed to complete booking', 400);
            }

            $booking = $this->bookingService->getBookingById($id);
            return $this->successResponse($booking, 'Booking completed successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByCustomer(int $customerId): JsonResponse
    {
        try {
            $bookings = $this->bookingService->getBookingsByCustomer($customerId);
            return $this->successResponse($bookings);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function checkAvailability(int $vehicleId): JsonResponse
    {
        try {
            $request = request();
            $request->validate([
                'pickup_date' => 'required|date',
                'return_date' => 'required|date|after:pickup_date',
            ]);

            $isAvailable = $this->bookingService->isVehicleAvailable(
                $vehicleId,
                $request->pickup_date,
                $request->return_date
            );

            return $this->successResponse([
                'available' => $isAvailable,
                'vehicle_id' => $vehicleId,
                'pickup_date' => $request->pickup_date,
                'return_date' => $request->return_date,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
