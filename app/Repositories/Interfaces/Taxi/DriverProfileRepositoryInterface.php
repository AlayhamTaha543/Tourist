<?php
namespace App\Repositories\Interfaces\Taxi;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;

interface DriverProfileRepositoryInterface
{
    public function create(array $data): Driver;
    public function update(int $driverId, array $data): Driver;
    public function delete(int $driverId): bool;
    public function getAll(): Collection;
    public function findById(int $id): ?Driver;
    public function findByTaxiServiceId(int $taxiServiceId): array;


}
