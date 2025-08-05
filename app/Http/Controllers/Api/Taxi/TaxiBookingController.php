<?php

namespace App\Http\Controllers\Api\Taxi;

use App\Http\Requests\TaxiBooking\ShowAllTaxiBookingRequest;
use App\Http\Requests\TaxiBooking\StoreTaxiBookingRequest;
use App\Http\Requests\TaxiBooking\ShowTaxiBookingRequest;
use App\Http\Requests\TaxiBooking\CancelTaxiBookingRequest;
use App\Http\Requests\TaxiBooking\AvailableTaxiServicesRequest;
use App\Http\Requests\TaxiBooking\AvailableVehicleTypesRequest;
use App\Http\Resources\TaxiBookingResource;
use App\Services\TaxiBooking\TaxiBookingService;
use App\Services\TaxiService\TaxiServiceManagementService;
use App\Services\Vehicle\VehicleTypeService;
use App\Exceptions\NoDriversAvailableException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Request;

class TaxiBookingController extends Controller
{
    protected $taxiBookingService;
    protected $taxiServiceManagementService;
    protected $vehicleTypeService;

    public function __construct(
        TaxiBookingService $taxiBookingService,
        TaxiServiceManagementService $taxiServiceManagementService,
        VehicleTypeService $vehicleTypeService
    ) {
        $this->taxiBookingService = $taxiBookingService;
        $this->taxiServiceManagementService = $taxiServiceManagementService;
        $this->vehicleTypeService = $vehicleTypeService;
        // $this->middleware('auth');
    }

    public function index(ShowAllTaxiBookingRequest $request): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();
            $id = $user->id;
            $taxiBookings = $this->taxiBookingService->getTaxiBookingsByUserId($id);

            return response()->json([
                'success' => true,
                'data' => TaxiBookingResource::collection($taxiBookings)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve user taxi bookings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve taxi bookings',
                $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreTaxiBookingRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = Auth::id();

            $taxiBooking = $this->taxiBookingService->bookTaxi(
                $data['taxi_service_id'],
                $data['pickup_date_time'], // Changed from pickup_time
                $data['pickup_location']['Latitude'],
                $data['pickup_location']['Longitude'],
                $data['radius'] ?? 10, // Add default radius if not provided
                $data
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Taxi booking created successfully',
                'data' => new TaxiBookingResource($taxiBooking)
            ], 201);
        } catch (NoDriversAvailableException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'No drivers available for this booking'
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create taxi booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to create taxi booking: ' . $e->getMessage()
            ], 500);
        }
    }


    public function show(ShowTaxiBookingRequest $request): JsonResponse
    {
        try {
            $taxiBooking = $this->taxiBookingService->getTaxiBookingById($request->id);

            if ($taxiBooking->booking->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => new TaxiBookingResource($taxiBooking)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Taxi booking not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve taxi booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve taxi booking'
            ], 500);
        }
    }

    public function cancel(CancelTaxiBookingRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $taxiBooking = $this->taxiBookingService->getTaxiBookingById($request->id);

            if ($taxiBooking->booking->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            $updatedBooking = $this->taxiBookingService->updateTaxiBooking($request->id, [
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Taxi booking cancelled successfully',
                'data' => new TaxiBookingResource($updatedBooking)
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Taxi booking not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel taxi booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to cancel taxi booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function availableTaxiServices(AvailableTaxiServicesRequest $request): JsonResponse
    {
        try {
            $locationId = $this->getNearestLocationId(
                $request->latitude,
                $request->longitude
            );

            $taxiServices = $this->taxiServiceManagementService->getTaxiServicesByLocation($locationId);

            return response()->json([
                'success' => true,
                'data' => $taxiServices
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get available taxi services: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get available taxi services: ' . $e->getMessage()
            ], 500);
        }
    }

    public function availableVehicleTypes(AvailableVehicleTypesRequest $request): JsonResponse
    {
        try {
            $vehicleTypes = $this->vehicleTypeService->getVehicleTypesByTaxiService($request->taxi_service_id);

            return response()->json([
                'success' => true,
                'data' => $vehicleTypes
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get available vehicle types: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get available vehicle types: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function getNearestLocationId(float $latitude, float $longitude): int
    {
        return 1; // Simplified implementation
    }
}
