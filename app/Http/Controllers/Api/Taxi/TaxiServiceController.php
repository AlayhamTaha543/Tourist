<?php

namespace App\Http\Controllers\Api\Taxi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaxiServiceRequest;
use App\Http\Requests\Taxi\ShowTaxiServiceRequest;
use App\Http\Requests\Taxi\UpdateTaxiServiceRequest;
use App\Http\Requests\Taxi\DeleteTaxiServiceRequest;
use App\Http\Requests\Taxi\TaxiServicesByLocationRequest;
use App\Http\Resources\TaxiServiceResource;
use App\Services\TaxiService\TaxiServiceManagementService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class TaxiServiceController extends Controller
{
    public function __construct(
        protected TaxiServiceManagementService $service
    ) {
        // $this->middleware('auth:sanctum');
        // $this->middleware('can:manage_taxi_services')->except(['index', 'show', 'getByLocation']);
    }

    /**
     * Get all taxi services
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 15);
        $services = $this->service->paginateTaxiServices($perPage, true);

        return TaxiServiceResource::collection($services)
            ->additional([
                'meta' => [
                    'total' => $services->total(),
                    'current_page' => $services->currentPage(),
                ]
            ]);
    }

    /**
     * Create a new taxi service
     */
    public function store(TaxiServiceRequest $request): JsonResponse
    {
        try {
            $service = $this->service->createTaxiService($request->validated());

            return (new TaxiServiceResource($service))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get taxi service details
     */
    public function show(ShowTaxiServiceRequest $request): JsonResponse
    {
        try {
            $service = $this->service->getFullServiceDetails($request->id);
            return (new TaxiServiceResource($service))->response();

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Taxi service not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update a taxi service
     */
    public function update(UpdateTaxiServiceRequest $request): JsonResponse
    {
        try {
            $service = $this->service->updateTaxiService($request->id, $request->validated());
            return (new TaxiServiceResource($service))->response();

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Taxi service not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Delete a taxi service
     */
    public function destroy(DeleteTaxiServiceRequest $request): JsonResponse
    {
        try {
            $this->service->deleteTaxiService($request->id);
            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Taxi service not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get taxi services by location
     */
    public function getByLocation(TaxiServicesByLocationRequest $request)
    {
        try {
            $services = $this->service->getTaxiServicesByLocation($request->location_id);
            return TaxiServiceResource::collection($services);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    private function errorResponse(string $message, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ], $code);
    }
}
