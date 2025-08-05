<?php
namespace App\Repositories\Interfaces\Taxi;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
interface DriverAvailabilityRepositoryInterface
{
    public function setOnline(int $driverId): void;
    public function setOffline(int $driverId): void;
    public function isOnline(int $driverId): bool;
    public function getAvailableDrivers(): Collection;
    public function getAvailableDriversWithinShift(string $time): Collection;
    public function getDriversByStatus(string $status): Collection;
    public function updateAvailability(int $driverId, string $status): bool;
    public function isDriverAvailableAtTime(int $driverId, Carbon $time): bool;
}
