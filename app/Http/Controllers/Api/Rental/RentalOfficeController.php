<?php

namespace App\Http\Controllers\Api\Rental;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Rental\ShowRentalOfficeRequest;
use App\Http\Requests\Rental\StoreRentalOfficeRequest;
use App\Http\Requests\Rental\UpdateRentalOfficeRequest;
use App\Http\Resources\RentalOfficeResource;
use App\Services\Rental\RentalOfficeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RentalOfficeController extends BaseController
{
    protected $officeService;

    public function __construct(RentalOfficeService $officeService)
    {
        $this->officeService = $officeService;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 15);
        $offices = $this->officeService->getPaginatedOffices($perPage);

        return RentalOfficeResource::collection($offices)
            ->additional([
                'meta' => [
                    'total' => $offices->total(),
                    'current_page' => $offices->currentPage(),
                    'last_page' => $offices->lastPage(),
                    'per_page' => $offices->perPage(),
                ]
            ]);
    }
    public function showAllRentalOffice()
    {
        $user = auth()->user();
        return $this->officeService->showAllRentalOffice(false, $user);
    }

    public function showNextTripRentalOffice()
    {
        $user = auth()->user();
        return $this->officeService->showAllRentalOffice(true, $user);
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

    public function show(ShowRentalOfficeRequest $request): JsonResponse
    {
        try {
            $office = $this->officeService->getOfficeById($request->id, true);
            return (new RentalOfficeResource($office))->response();

            if (!$office) {
                return $this->resourceNotFound('Rental office');
            }

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
