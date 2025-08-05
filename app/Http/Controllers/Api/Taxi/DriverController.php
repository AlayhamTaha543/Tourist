<?php

namespace App\Http\Controllers\Api\Taxi;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Services\Driver\DriverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    protected $driverService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\Driver\DriverService $driverService
     * @return void
     */
    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Get nearby drivers for a location.
     */
    public function getNearbyDrivers(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50',
            'taxiServiceId' => 'nullable|exists:taxi_services,id',
            'bookingDateTime' => 'nullable|date'
        ]);

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius', 5); // Default 5km radius
        $taxiServiceId = $request->input('taxiServiceId');
        $bookingDateTime = $request->input('bookingDateTime');

        try {
            $drivers = $this->driverService->getAvailableDriversForBooking(
                $taxiServiceId,
                $bookingDateTime,
                $latitude,
                $longitude,
                $radius
            );

            return response()->json([
                'data' => $drivers,/* to use the resource DriverResource::collection($drivers)*/
                'meta' => [
                    'count' => count($drivers),
                    'radius_km' => $radius
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get nearby drivers: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to get nearby drivers.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available drivers for booking.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function available(Request $request): JsonResponse
    {
        $request->validate([
            'pickup_datetime' => 'required|date|after:now',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50'
        ]);

        $pickupDatetime = $request->input('pickup_datetime');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius', 5); // Default 5km radius

        try {
            $drivers = $this->driverService->getBookableNearbyDrivers(
                $pickupDatetime,
                $latitude,
                $longitude,
                $radius
            );

            return response()->json([
                'data' => $drivers,
                'meta' => [
                    'count' => count($drivers),
                    'radius_km' => $radius
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get available drivers: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to get available drivers.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver statistics.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(int $id): JsonResponse
    {
        try {
            // Verify the driver exists
            $driver = $this->driverService->getDriverById($id);

            // Get driver stats
            $stats = $this->driverService->getDriverStats($id);

            return response()->json([
                'data' => $stats
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Driver not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to get driver stats: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to get driver statistics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update driver location.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLocation(Request $request, int $id): JsonResponse
    {
        $this->checkDriverOwnership($id);
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        try {
            // Verify the driver exists
            $driver = $this->driverService->getDriverById($id);

            // Update location
            $result = $this->driverService->updateDriverLocation(
                $id,
                $request->input('latitude'),
                $request->input('longitude')
            );

            return response()->json([
                'message' => 'Driver location updated successfully.',
                'success' => $result
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Driver not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to update driver location: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update driver location.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update driver availability status.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvailability(Request $request, int $id): JsonResponse
    {
        $this->checkDriverOwnership($id);
        $request->validate([
            'status' => 'required|string|in:available,busy,offline'
        ]);

        try {
            // Verify the driver exists
            $driver = $this->driverService->getDriverById($id);

            // Update availability based on status
            $status = $request->input('status');
            $result = false;

            switch ($status) {
                case 'available':
                    $result = $this->driverService->markAvailable($id);
                    break;
                case 'busy':
                    $result = $this->driverService->markBusy($id);
                    break;
                case 'offline':
                    $result = $this->driverService->markOffline($id);
                    break;
            }

            return response()->json([
                'message' => 'Driver availability updated successfully.',
                'success' => $result
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Driver not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to update driver availability: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update driver availability.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    protected function checkDriverOwnership(int $driverId)
    {
        if (Auth::user()->driver->id !== $driverId) {
            abort(403, 'Unauthorized action.');
        }
    }

}
