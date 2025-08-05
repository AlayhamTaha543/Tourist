<?php
namespace App\Repositories\Interfaces\Taxi;

use Illuminate\Support\Collection;

interface DriverLocationRepositoryInterface
{
    public function updateLocation(int $driverId, float $lat, float $lng): bool;

    public function getNearbyDrivers(float $lat, float $lng, int $radius): Collection;

    public function getNearbyDriversByTaxiService(int $taxiServiceId, float $lat, float $lng, int $radius, ?int $vehicleTypeId = null): Collection;
}
