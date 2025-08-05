<?php

namespace App\Http\Controllers\Api\Rental;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Rental\StoreRentalOfficeRequest;
use App\Http\Requests\Rental\UpdateRentalOfficeRequest;
use App\Services\Rental\RentalOfficeService;
use Illuminate\Http\JsonResponse;

class RentalOfficeController extends BaseController
{
    protected $officeService;

    public function __construct(RentalOfficeService $officeService)
    {
        $this->officeService = $officeService;
    }

    public function index(): JsonResponse
    {
        try {
            $offices = $this->officeService->getPaginatedOffices();
            return $this->successResponse($offices);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreRentalOfficeRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $office = $this->officeService->createOffice($validated);
            return $this->successResponse($office, 'Office created successfully', 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $office = $this->officeService->getOfficeById($id, true);

            if (!$office) {
                return $this->resourceNotFound('Rental office');
            }

            return $this->successResponse($office);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateRentalOfficeRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (!$this->officeService->updateOffice($id, $validated)) {
                return $this->resourceNotFound('Rental office');
            }

            $updatedOffice = $this->officeService->getOfficeById($id);
            return $this->successResponse($updatedOffice, 'Office updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            if (!$this->officeService->deleteOffice($id)) {
                return $this->resourceNotFound('Rental office');
            }

            return $this->successResponse(null, 'Office deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByLocation(int $locationId): JsonResponse
    {
        try {
            $offices = $this->officeService->getOfficesByLocation($locationId);
            return $this->successResponse($offices);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
