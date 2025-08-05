<?php
namespace App\Repositories\Impl\Driver;

use App\Models\Driver;
use App\Repositories\Interfaces\Taxi\DriverLocationRepositoryInterface;
use App\Repositories\Impl\Driver\DriverAvailabilityRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DriverLocationRepository implements DriverLocationRepositoryInterface
{
    protected DriverAvailabilityRepository $availabilityRepository;

    public function __construct(DriverAvailabilityRepository $availabilityRepository)
    {
        $this->availabilityRepository = $availabilityRepository;
    }

    /**
     * Update the driver's current location and timestamps
     */
    public function updateLocation(int $driverId, float $lat, float $lng): bool
    {
        try {

            $sql = "UPDATE drivers
                    SET current_location = ST_MakePoint(?, ?, 4326),
                        location_updated_at = ?,
                        last_seen_at = ?
                    WHERE id = ?";
            $bindings = [$lng, $lat, now(), now(), $driverId];

            $affected = DB::affectingStatement($sql, $bindings);

            if ($affected === 0) {
                Log::warning("Driver location update affected 0 rows", [
                    'driver_id' => $driverId,
                    'lat' => $lat,
                    'lng' => $lng,
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to update driver location", [
                'driver_id' => $driverId,
                'lat' => $lat,
                'lng' => $lng,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Base query to find drivers within a radius using Laravel Spatial scopes
     */
    private function baseNearbyQuery(float $lat, float $lng, int $radius)
    {
        return Driver::query()
            ->select([
                'id',
                'taxi_service_id',
                DB::raw('ST_Y(current_location) as lat'),
                DB::raw('ST_X(current_location) as lng'),
                'availability_status',
            ])
            ->available()
            ->selectDistanceTo('current_location', [$lng, $lat], 'distance')
            ->withinDistanceTo('current_location', [$lng, $lat], $radius)
            ->orderBy('distance');
    }

    /**
     * Get all available drivers near a location
     */
    public function getNearbyDrivers(float $lat, float $lng, int $radius): Collection
    {
        try {
            $drivers = $this->baseNearbyQuery($lat, $lng, $radius)
                ->recentlyActive()
                ->get();

            return $drivers->filter(function ($driver) {
                return $this->availabilityRepository
                    ->isDriverAvailableAtTime($driver->id, Carbon::now());
            });
        } catch (\Throwable $e) {
            Log::error("Error fetching nearby drivers", [
                'lat' => $lat,
                'lng' => $lng,
                'radius' => $radius,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return collect();
        }
    }

    /**
     * Get nearby drivers filtered by taxi service
     */
    public function getNearbyDriversByTaxiService(
        int $taxiServiceId,
        float $lat,
        float $lng,
        int $radius,
        ?int $vehicleTypeId = null
    ): Collection {
        try {
            $query = $this->baseNearbyQuery($lat, $lng, $radius)
                ->where('taxi_service_id', $taxiServiceId)
                ->recentlyActive();

            // Conditionally add vehicle type filter
            if ($vehicleTypeId !== null) {
                $query->whereHas('activeVehicle', function ($q) use ($vehicleTypeId) {
                    $q->where('vehicle_type_id', $vehicleTypeId);
                });
            }

            $drivers = $query->get();

            return $drivers->filter(function ($driver) {
                return $this->availabilityRepository
                    ->isDriverAvailableAtTime($driver->id, Carbon::now());
            });
        } catch (\Throwable $e) {
            Log::error("Error fetching drivers for taxi service", [
                'taxi_service_id' => $taxiServiceId,
                'vehicle_type_id' => $vehicleTypeId,
                'lat' => $lat,
                'lng' => $lng,
                'radius' => $radius,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return collect();
        }
    }
}
