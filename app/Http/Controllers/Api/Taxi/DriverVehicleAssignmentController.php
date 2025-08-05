<?php

namespace App\Http\Controllers\Api\Taxi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\AssignDriverRequest;
use App\Http\Resources\DriverVehicleAssignmentResource;
use App\Services\Driver\DriverVehicleAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class DriverVehicleAssignmentController extends Controller
{
    protected DriverVehicleAssignmentService $assignmentService;

    public function __construct(DriverVehicleAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    public function assign(AssignDriverRequest $request): JsonResponse
    {
        try {
            $assignment = $this->assignmentService->assign(
                $request->validated()['driver_id'],
                $request->validated()['vehicle_id']
            );

            return response()->json(new DriverVehicleAssignmentResource($assignment), 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function unassign(int $assignmentId): JsonResponse
    {
        try {
            $this->assignmentService->unassign($assignmentId);
            return response()->json(['message' => 'Driver unassigned successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Assignment not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function activeAssignmentsByDriver(int $driverId): JsonResponse
    {
        $assignments = $this->assignmentService->listActiveByDriver($driverId);
        return response()->json(DriverVehicleAssignmentResource::collection($assignments));
    }

    public function activeAssignmentsByVehicle(int $vehicleId): JsonResponse
    {
        $assignments = $this->assignmentService->listActiveByVehicle($vehicleId);
        return response()->json(DriverVehicleAssignmentResource::collection($assignments));
    }

    public function history(Request $request): JsonResponse
    {
        $filters = $request->only(['driver_id', 'vehicle_id']);
        $perPage = $request->get('per_page', 15);

        $history = $this->assignmentService->history($filters, $perPage);
        return response()->json(DriverVehicleAssignmentResource::collection($history));
    }

    public function checkDriverAvailability(int $driverId): JsonResponse
    {
        $available = $this->assignmentService->checkDriverAvailable($driverId);
        return response()->json(['available' => $available]);
    }

    public function checkVehicleAvailability(int $vehicleId): JsonResponse
    {
        $available = $this->assignmentService->checkVehicleAvailable($vehicleId);
        return response()->json(['available' => $available]);
    }

    public function endAllAssignmentsForDriver(int $driverId): JsonResponse
    {
        $count = $this->assignmentService->endAllForDriver($driverId);
        return response()->json(['message' => "Ended {$count} active assignments for driver."]);
    }

    public function endAllAssignmentsForVehicle(int $vehicleId): JsonResponse
    {
        $count = $this->assignmentService->endAllForVehicle($vehicleId);
        return response()->json(['message' => "Ended {$count} active assignments for vehicle."]);
    }

    public function getAssignmentDetails(int $assignmentId): JsonResponse
    {
        try {
            $assignment = $this->assignmentService->getById($assignmentId);
            if (!$assignment) {
                return response()->json(['error' => 'Assignment not found.'], 404);
            }
            return response()->json(new DriverVehicleAssignmentResource($assignment));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
