<?php

namespace App\Repositories\Interfaces\Rent;

use App\Enum\RentalBookingStatus;
use App\Models\RentalBooking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RentalBookingRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?RentalBooking;
    public function create(array $data): RentalBooking;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByCustomer(int $customerId): Collection;
    public function findByVehicle(int $vehicleId): Collection;
    public function findByOffice(int $officeId): Collection;
    public function updateStatus(int $bookingId, RentalBookingStatus $status): bool;
    public function findActiveBookings(?int $vehicleId = null): Collection;

}
