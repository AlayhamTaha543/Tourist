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
use App\Services\GeoapifyService; // Import GeoapifyService
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Request;
use App\Models\Location; // Import Location model

class TaxiBookingController extends Controller
{
    protected $taxiBookingService;
    protected $taxiServiceManagementService;
    protected $vehicleTypeService;
    protected $geoapifyService; // Declare GeoapifyService

    public function __construct(
        TaxiBookingService $taxiBookingService,
        TaxiServiceManagementService $taxiServiceManagementService,
        VehicleTypeService $vehicleTypeService,
        GeoapifyService $geoapifyService // Inject GeoapifyService
    ) {
        $this->taxiBookingService = $taxiBookingService;
        $this->taxiServiceManagementService = $taxiServiceManagementService;
        $this->vehicleTypeService = $vehicleTypeService;
        $this->geoapifyService = $geoapifyService; // Assign GeoapifyService
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

            $pickupLat = 0;
            $pickupLng = 0;
            $dropoffLat = 0;
            $dropoffLng = 0;

            // Resolve Pickup Location Coordinates
            if (isset($data['pickup_location']['latitude']) && isset($data['pickup_location']['longitude'])) {
                $pickupLat = $data['pickup_location']['latitude'];
                $pickupLng = $data['pickup_location']['longitude'];
            } elseif (isset($data['pickup_address'])) {
                $geocodeResult = $this->geoapifyService->geocodeAddress($data['pickup_address']);
                if ($geocodeResult) {
                    $pickupLat = $geocodeResult['lat'];
                    $pickupLng = $geocodeResult['lon'];
                } else {
                    return response()->json(['success' => false, 'error' => 'Could not geocode pickup address'], 422);
                }
            } elseif (isset($data['pickup_location_id'])) {
                $location = Location::find($data['pickup_location_id']);
                if ($location) {
                    $pickupLat = $location->latitude;
                    $pickupLng = $location->longitude;
                } else {
                    return response()->json(['success' => false, 'error' => 'Pickup location ID not found'], 422);
                }
            } else {
                return response()->json(['success' => false, 'error' => 'Pickup location is required'], 422);
            }

            // Resolve Dropoff Location Coordinates
            if (isset($data['dropoff_location']['latitude']) && isset($data['dropoff_location']['longitude'])) {
                $dropoffLat = $data['dropoff_location']['latitude'];
                $dropoffLng = $data['dropoff_location']['longitude'];
            } elseif (isset($data['dropoff_address'])) {
                $geocodeResult = $this->geoapifyService->geocodeAddress($data['dropoff_address']);
                if ($geocodeResult) {
                    $dropoffLat = $geocodeResult['lat'];
                    $dropoffLng = $geocodeResult['lon'];
                } else {
                    return response()->json(['success' => false, 'error' => 'Could not geocode dropoff address'], 422);
                }
            } elseif (isset($data['dropoff_location_id'])) {
                $location = Location::find($data['dropoff_location_id']);
                if ($location) {
                    $dropoffLat = $location->latitude;
                    $dropoffLng = $location->longitude;
                } else {
                    return response()->json(['success' => false, 'error' => 'Dropoff location ID not found'], 422);
                }
            }
            // If dropoff is not provided, it defaults to 0,0 which is handled by the service method.

            $taxiBooking = $this->taxiBookingService->bookTaxi(
                $data['taxi_service_id'],
                $data['pickup_date_time'],
                $pickupLat,
                $pickupLng,
                $dropoffLat,
                $dropoffLng,
                $data['radius'] ?? 10,
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