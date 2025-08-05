<?php

namespace App\Repositories\Impl\Rent;

use App\Enum\RentalBookingStatus;
use App\Models\RentalBooking;
use App\Repositories\Interfaces\Rent\RentalBookingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RentalBookingRepositoryImpl implements RentalBookingRepositoryInterface
{
    public function all(): Collection
    {
        return RentalBooking::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return RentalBooking::with(['customer', 'vehicle', 'office'])
            ->paginate($perPage);
    }

    public function find(int $id): ?RentalBooking
    {
        return RentalBooking::find($id);
    }

    public function create(array $data): RentalBooking
    {
        return RentalBooking::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $booking = RentalBooking::find($id);
        return $booking ? $booking->update($data) : false;
    }

    public function delete(int $id): bool
    {
        return RentalBooking::destroy($id) > 0;
    }

    public function findByCustomer(int $customerId): Collection
    {
        return RentalBooking::where('customer_id', $customerId)
            ->orderBy('pickup_date', 'desc')
            ->get();
    }

    public function findByVehicle(int $vehicleId): Collection
    {
        return RentalBooking::where('vehicle_id', $vehicleId)
            ->orderBy( 'pickup_date', 'desc')
            ->get();
    }

    public function findByOffice(int $officeId): Collection
    {
        return RentalBooking::where('office_id', $officeId)
            ->orderBy('pickup_date', 'desc')
            ->get();
    }

    public function updateStatus(int $bookingId, RentalBookingStatus $status): bool
    {
        $booking = RentalBooking::find($bookingId);

        if (!$booking) {
            return false;
        }

        $booking->status = $status;
        return $booking->save();
    }

    public function findActiveBookings(?int $vehicleId = null): Collection
    {
        $query = RentalBooking::whereIn('status', [
            RentalBookingStatus::RESERVED,
            RentalBookingStatus::ACTIVE
        ]);

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        return $query->get();
    }
}
