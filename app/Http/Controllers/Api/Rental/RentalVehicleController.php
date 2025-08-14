<?php

namespace App\Http\Controllers\Api\Rental;

use App\Enum\RentalVehicleStatus;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Rental\StoreRentalVehicleRequest;
use App\Http\Requests\Rental\UpdateRentalVehicleRequest;
use App\Http\Requests\Rental\UpdateVehicleStatusRequest;
use App\Http\Resources\RentalVehicleResource;
use App\Services\Rental\RentalVehicleService;
use Illuminate\Http\JsonResponse;

class RentalVehicleController extends BaseController
{
    protected $vehicleService;

    public function __construct(RentalVehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    public function index(): JsonResponse
    {
        try {
            $vehicles = $this->vehicleService->getPaginatedVehicles();

            // Transform the paginated data using the resource
            $transformedVehicles = $vehicles->toArray();
            $transformedVehicles['data'] = RentalVehicleResource::collection($vehicles->items())->toArray(request());

            return $this->successResponse($transformedVehicles);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreRentalVehicleRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $vehicle = $this->vehicleService->createVehicle($validated);
            return $this->successResponse($vehicle, 'Vehicle created successfully', 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $vehicle = $this->vehicleService->getVehicleById($id);

            if (!$vehicle) {
                return $this->resourceNotFound('Vehicle');
            }

            return $this->successResponse($vehicle);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateRentalVehicleRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (!$this->vehicleService->updateVehicle($id, $validated)) {
                return $this->resourceNotFound('Vehicle');
            }

            $updatedVehicle = $this->vehicleService->getVehicleById($id);
            return $this->successResponse($updatedVehicle, 'Vehicle updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            if (!$this->vehicleService->deleteVehicle($id)) {
                return $this->resourceNotFound('Vehicle');
            }

            return $this->successResponse(null, 'Vehicle deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function updateStatus(UpdateVehicleStatusRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $status = RentalVehicleStatus::from($validated['status']);

            if (!$this->vehicleService->updateVehicleStatus($id, $status)) {
                return $this->resourceNotFound('Vehicle');
            }

            $vehicle = $this->vehicleService->getVehicleById($id);
            return $this->successResponse($vehicle, 'Vehicle status updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getStatusHistory(int $id): JsonResponse
    {
        try {
            $history = $this->vehicleService->getVehicleStatusHistory($id);
            return $this->successResponse($history);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByOffice(int $officeId): JsonResponse
    {
        try {
            $vehicles = $this->vehicleService->getVehiclesByOffice($officeId);
            return $this->successResponse($vehicles);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
