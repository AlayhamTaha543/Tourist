<?php
namespace App\Http\Controllers\Api\Taxi;

use App\Filament\Resources\VehicleTypeResource as ResourcesVehicleTypeResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\StoreVehicleTypeRequest;
use App\Http\Requests\Vehicle\UpdateVehicleTypeRequest;
use App\Http\Resources\VehicleTypeResource;
use App\Models\VehicleType;
use App\Services\Vehicle\VehicleTypeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class VehicleTypeController extends Controller
{
    public function __construct(
        protected VehicleTypeService $vehicleTypeService
    ) {
        $this->middleware('auth:sanctum');
        $this->middleware('can:manage_vehicle_types')->except(['index', 'show', 'getByTaxiService']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $vehicleTypes = $this->vehicleTypeService->getAllVehicleTypes();

            return ResourcesVehicleTypeResource::collection($vehicleTypes)
                ->additional(['message' => 'Vehicle types retrieved successfully'])
                ->response();
        } catch (\Exception $e) {
            Log::error('Failed to retrieve vehicle types: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve vehicle types', $e);
        }
    }

    public function store(StoreVehicleTypeRequest $request): JsonResponse
    {
        try {
            Gate::authorize('create', VehicleType::class);

            $vehicleType = $this->vehicleTypeService->createVehicleType($request->validated());

            return (new VehicleTypeResource($vehicleType))
                ->additional(['message' => 'Vehicle type created successfully'])
                ->response()->setStatusCode(201);
        } catch (\Exception $e) {
            Log::error('Vehicle type creation failed: ' . $e->getMessage());
            return $this->errorResponse('Vehicle type creation failed', $e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $vehicleType = $this->vehicleTypeService->getVehicleTypeById($id);
            return (new VehicleTypeResource($vehicleType))
                ->additional(['message' => 'Vehicle type retrieved successfully'])
                ->response();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Vehicle type not found');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve vehicle type', $e);
        }
    }

    public function update(UpdateVehicleTypeRequest $request, int $id): JsonResponse
    {
        try {
            $vehicleType = $this->vehicleTypeService->getVehicleTypeById($id);
            Gate::authorize('update', $vehicleType);

            $updated = $this->vehicleTypeService->updateVehicleType($id, $request->validated());

            return (new VehicleTypeResource($updated))
                ->additional(['message' => 'Vehicle type updated successfully'])
                ->response();
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Vehicle type not found');
        } catch (\Exception $e) {
            return $this->errorResponse('Vehicle type update failed', $e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $vehicleType = $this->vehicleTypeService->getVehicleTypeById($id);
            Gate::authorize('delete', $vehicleType);

            $this->vehicleTypeService->deleteVehicleType($id);
            return response()->json([
                'message' => 'Vehicle type deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Vehicle type not found');
        } catch (\Exception $e) {
            return $this->errorResponse('Vehicle type deletion failed', $e);
        }
    }

    public function getByTaxiService(int $taxiServiceId): JsonResponse
    {
        try {
            $vehicleTypes = $this->vehicleTypeService->getVehicleTypesByTaxiService($taxiServiceId);
            return VehicleTypeResource::collection($vehicleTypes)
                ->additional(['message' => 'Vehicle types retrieved successfully'])
                ->response();
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve vehicle types', $e);
        }
    }

    // Helper methods
    private function errorResponse(string $message, \Exception $e, int $code = 500): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'error' => config('app.debug') ? $e->getMessage() : null
        ], $code);
    }

    private function notFoundResponse(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 404);
    }
}
