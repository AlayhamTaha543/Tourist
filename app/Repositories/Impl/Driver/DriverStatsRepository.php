<?php
namespace App\Repositories\Impl\Driver;


use App\Models\Driver;
use App\Models\Trip;
use App\Repositories\Interfaces\Taxi\DriverStatsRepositoryInterface;
class DriverStatsRepository implements DriverStatsRepositoryInterface
{
    public function getEarnings(int $driverId, string $from, string $to): float
    {
        return Trip::where('id', $driverId)
            ->whereBetween('completed_at', [$from, $to])
            ->sum('fare');
    }

    public function getTripCount(int $driverId): int
    {
        return Trip::where('driver_id', $driverId)->count();
    }

    public function getRating(int $driverId): float
    {
        return Driver::where('id', $driverId)->avg('rating') ?? 0.0;
    }
}
