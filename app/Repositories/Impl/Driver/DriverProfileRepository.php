<?php

namespace App\Repositories\Impl\Driver;


use App\Models\Driver;
use App\Repositories\Interfaces\Taxi\DriverProfileRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DriverProfileRepository implements DriverProfileRepositoryInterface
{
    public function getAll(): Collection
    {
        return Driver::all();
    }

    public function create(array $data): Driver
    {
        return Driver::create($data);
    }

    public function update(int $driverId, array $data): Driver
    {
        $driver = $this->findById($driverId);
        $driver->update($data);
        return $driver;
    }

    public function delete(int $driverId): bool
    {
        try {
            $driver = $this->findById($driverId);
            if (!$driver) {
                throw new \Exception('Driver not found');
            }
            return $driver->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function findById(int $id): ?Driver
    {
        return Driver::findOrFail($id);
    }
    public function findByTaxiServiceId(int $taxiServiceId): array
    {
        return Driver::where('taxi_service_id', $taxiServiceId)->get()->toArray();
    }
}
