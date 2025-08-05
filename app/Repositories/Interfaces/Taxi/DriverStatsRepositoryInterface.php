<?php
namespace App\Repositories\Interfaces\Taxi;
interface DriverStatsRepositoryInterface {
    public function getEarnings(int $driverId, string $from, string $to): float;
    public function getTripCount(int $driverId): int;
    public function getRating(int $driverId): float;
}
