<?php

namespace App\Services\Vehicle;

use App\Models\Vehicle;
use App\Repositories\Impl\TaxiServiceRepository;
use App\Repositories\Impl\VehicleRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VehicleService
{
    protected $vehicleTypeService;
    protected $vehicleRepository;
    protected $taxiServiceRepository;

    /**
     * Create a new service instance.
     *
     * @param VehicleRepository $vehicleRepository
     * @return void
     */
    public function __construct(VehicleRepository $vehicleRepository, VehicleTypeService $vehicleTypeService, TaxiServiceRepository $taxiServiceRepository)
    {

        $this->vehicleTypeService = $vehicleTypeService;
        $this->vehicleRepository = $vehicleRepository;
        $this->taxiServiceRepository = $taxiServiceRepository;
    }

    /**
     * Get all vehicles
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllVehicles(): Collection
    {
        return $this->vehicleRepository->all();
    }

    /**
     * Get a vehicle by ID
     *
     * @param int $id
     * @return \App\Models\Vehicle
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getVehicleById(int $id): Vehicle
    {
        return $this->vehicleRepository->findOrFail($id);
    }

    /**
     * Get vehicles by taxi service
     *
     * @param int $taxiServiceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehiclesByTaxiService(int $taxiServiceId): Collection
    {
        if (!$this->taxiServiceRepository->find($taxiServiceId)) {
            throw new \RuntimeException("Taxi service with ID {$taxiServiceId} does not exist");
        }
        return $this->vehicleRepository->getByTaxiService($taxiServiceId);
    }

    /**
     * Get vehicles by vehicle type
     *
     * @param int $vehicleTypeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehiclesByType(int $vehicleTypeId): Collection
    {
        return $this->vehicleRepository->getByVehicleType($vehicleTypeId);
    }

    /**
     * Get available vehicles for booking
     *
     * @param int $taxiServiceId
     * @param int $vehicleTypeId
     * @param string $bookingDateTime
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableVehicles(int $taxiServiceId, int $vehicleTypeId, string $bookingDateTime): Collection
    {
        $bookingTime = Carbon::parse($bookingDateTime);

        $vehicles = $this->vehicleRepository->getByTaxiServiceAndType($taxiServiceId, $vehicleTypeId);

        return $vehicles->filter(function ($vehicle) use ($bookingTime) {
            return $this->isVehicleAvailable($vehicle, $bookingTime);
        });
    }

    /**
     * Create a new vehicle
     *
     * @param array $data
     * @return \App\Models\Vehicle
     * @throws \Exception
     */
    public function createVehicle(array $data): Vehicle
    {
        try {
            DB::beginTransaction();

            // Validate vehicle type belongs to the taxi service
            $type = $this->vehicleTypeService->getVehicleTypeById($data['vehicle_type_id']);
            if ($type->taxi_service_id !== $data['taxi_service_id']) {
                throw new \InvalidArgumentException('Vehicle type doesn\'t belong to this taxi service');
            }

            $vehicle = $this->vehicleRepository->create($data);

            // Broadcast the creation event
            event(new \App\Events\VehicleCreated($vehicle));

            DB::commit();
            return $vehicle;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create vehicle: ' . $e->getMessage());
            throw new \Exception('Error occurred while creating vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Update a vehicle
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Vehicle
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function updateVehicle(int $id, array $data): Vehicle
    {
        try {
            DB::beginTransaction();

            $result = $this->vehicleRepository->update($id, $data);

            // Get the updated vehicle and broadcast the event
            if ($result) {
                $vehicle = $this->vehicleRepository->findOrFail($id);
                event(new \App\Events\VehicleUpdated($vehicle));
            } else {
                throw new \Exception('Failed to update vehicle');
            }

            DB::commit();
            return $vehicle;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new ModelNotFoundException('Vehicle not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update vehicle: ' . $e->getMessage());
            throw new \Exception('Error occurred while updating vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Delete a vehicle
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function deleteVehicle(int $id): bool
    {
        try {
            DB::beginTransaction();

            // Get the vehicle before deletion for event broadcasting
            $vehicle = $this->vehicleRepository->findOrFail($id);
            $result = $this->vehicleRepository->delete($id);

            if ($result) {
                // Broadcast deletion event if needed
                // event(new \App\Events\VehicleDeleted($vehicle->id));
            }

            DB::commit();
            return $result;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new ModelNotFoundException('Vehicle not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete vehicle: ' . $e->getMessage());
            throw new \Exception('Error occurred while deleting vehicle: ' . $e->getMessage());
        }
    }
    public function isVehicleAvailable(Vehicle $vehicle, Carbon $bookingTime): bool
    {
        return !$vehicle->trips()
            ->whereBetween('started_at', [
                $bookingTime->copy()->subHours(1),
                $bookingTime->copy()->addHours(1)
            ])
            ->exists();
    }

    public function isVehicleAvailableForBooking(int $vehicleId, Carbon $pickupDateTime, int $durationMinutes): bool
    {
        $bookingStart = $pickupDateTime;
        $bookingEnd = $pickupDateTime->copy()->addMinutes($durationMinutes);

        return Vehicle::where('id', $vehicleId)
            ->whereDoesntHave('taxiBookings', function ($query) use ($bookingStart, $bookingEnd) {
                $query->where(function ($q) use ($bookingStart, $bookingEnd) {
                    // Check for overlapping taxi bookings
                    $q->where('status', '!=', 'cancelled')
                        ->where('status', '!=', 'completed')
                        ->where(function ($q2) use ($bookingStart, $bookingEnd) {
                        $q2->whereBetween('pickup_date_time', [$bookingStart, $bookingEnd]) // Existing booking starts within new booking
                            ->orWhereBetween(DB::raw('DATE_ADD(pickup_date_time, INTERVAL duration_minutes MINUTE)'), [$bookingStart, $bookingEnd]) // Existing booking ends within new booking
                            ->orWhere(function ($q3) use ($bookingStart, $bookingEnd) { // New booking falls within an existing booking
                                $q3->where('pickup_date_time', '<=', $bookingStart)
                                    ->where(DB::raw('DATE_ADD(pickup_date_time, INTERVAL duration_minutes MINUTE)'), '>=', $bookingEnd);
                            });
                    });
                });
            })
            ->exists();
    }
}