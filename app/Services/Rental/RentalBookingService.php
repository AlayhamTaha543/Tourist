<?php

namespace App\Services\Rental;

use App\Enum\RentalBookingStatus;
use App\Repositories\Interfaces\Rent\RentalBookingRepositoryInterface;
use App\Services\Rental\RentalVehicleService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RentalBookingService
{
    public function __construct(
        protected RentalBookingRepositoryInterface $bookingRepository,
        protected RentalVehicleService $vehicleService
    ) {}

    public function getAllBookings(): Collection
    {
        return $this->bookingRepository->all();
    }

    public function getPaginatedBookings(int $perPage = 15): LengthAwarePaginator
    {
        return $this->bookingRepository->paginate($perPage);
    }

    public function getBookingById(int $id): ?object
    {
        return $this->bookingRepository->find($id);
    }

    public function createBooking(array $data): object
    {
        return DB::transaction(function () use ($data) {
            // Calculate total price
            $pickupDate = Carbon::parse($data['pickup_date']);
            $returnDate = Carbon::parse($data['return_date']);
            $days = $returnDate->diffInDays($pickupDate);

            $vehicle = $this->vehicleService->getVehicleById($data['vehicle_id']);

            // Ensure we have the vehicle's category loaded
            if (!$vehicle->relationLoaded('category')) {
                $vehicle->load('category');
            }

            $data['daily_rate'] = $vehicle->category->price_per_day;
            $data['total_price'] = $days * $vehicle->category->price_per_day;
            $data['status'] = RentalBookingStatus::RESERVED;

            $booking = $this->bookingRepository->create($data);

            // Update vehicle status to reserved
            $this->vehicleService->updateVehicleStatus(
                $data['vehicle_id'],
                \App\Enum\RentalVehicleStatus::RESERVED
            );

            return $booking;
        });
    }

    public function updateBooking(int $id, array $data): bool
    {
        return $this->bookingRepository->update($id, $data);
    }

    public function cancelBooking(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $booking = $this->bookingRepository->find($id);

            if (!$booking) {
                return false;
            }

            if ($booking->status !== RentalBookingStatus::CANCELLED) {
                // Update vehicle status back to available
                $this->vehicleService->updateVehicleStatus(
                    $booking->vehicle_id,
                    \App\Enum\RentalVehicleStatus::AVAILABLE
                );

                return $this->bookingRepository->updateStatus(
                    $id,
                    RentalBookingStatus::CANCELLED
                );
            }

            return false;
        });
    }

    public function completeBooking(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $booking = $this->bookingRepository->find($id);

            if (!$booking) {
                return false;
            }

            if ($booking->status === RentalBookingStatus::ACTIVE) {
                // Update vehicle status to available
                $this->vehicleService->updateVehicleStatus(
                    $booking->vehicle_id,
                    \App\Enum\RentalVehicleStatus::AVAILABLE
                );

                return $this->bookingRepository->updateStatus(
                    $id,
                    RentalBookingStatus::COMPLETED
                );
            }

            return false;
        });
    }

    public function startBooking(int $id): bool
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            return false;
        }

        if ($booking->status === RentalBookingStatus::RESERVED) {
            // Update booking status to active
            return $this->bookingRepository->updateStatus(
                $id,
                RentalBookingStatus::ACTIVE
            );
        }

        return false;
    }

    public function getBookingsByCustomer(int $customerId): Collection
    {
        return $this->bookingRepository->findByCustomer($customerId);
    }

    public function getActiveBookingsForVehicle(int $vehicleId): Collection
    {
        // Correctly call with vehicle ID parameter
        return $this->bookingRepository->findActiveBookings($vehicleId);
    }

    public function isVehicleAvailable(int $vehicleId, string $pickupDate, string $returnDate): bool
    {
        $activeBookings = $this->getActiveBookingsForVehicle($vehicleId);

        $pickup = Carbon::parse($pickupDate);
        $return = Carbon::parse($returnDate);

        foreach ($activeBookings as $booking) {
            $bookingPickup = Carbon::parse($booking->pickup_date);
            $bookingReturn = Carbon::parse($booking->return_date);

            if ($pickup->between($bookingPickup, $bookingReturn) ||
                $return->between($bookingPickup, $bookingReturn) ||
                ($pickup->lte($bookingPickup) && $return->gte($bookingReturn)))
            {
                return false;
            }
        }

        return true;
    }
}
