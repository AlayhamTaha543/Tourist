<?php

namespace App\Services\interfaces;

use App\Models\TaxiService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaxiServiceManagementServiceInterface
{
    ##              For Super Admin
    /**
     * Get all taxi services
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTaxiServices(): Collection;

    /**
     * Get paginated taxi services
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedTaxiServices(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get active taxi services
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveTaxiServices(): Collection;

    ##                   For Admin After the super admin adds him
    /**
     * Get a taxi service by ID
     *
     * @param int $id
     * @return \App\Models\TaxiService
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getTaxiServiceById(int $id): TaxiService;

    /**
     * Create a new taxi service
     *
     * @param array $data
     * @return \App\Models\TaxiService
     * @throws \Exception
     */
    public function createTaxiService(array $data): TaxiService;

    /**
     * Update a taxi service
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\TaxiService
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function updateTaxiService(int $id, array $data): TaxiService;

    /**
     * Delete a taxi service
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function deleteTaxiService(int $id): bool;

    ##                   USER
    /**
     * Update taxi service rating
     *
     * @param int $id
     * @param float $rating
     * @return \App\Models\TaxiService
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function updateTaxiServiceRating(int $id, float $rating): TaxiService;

    ##                  NEARBY TAXI SERVICES FROM THE USER
    /**
     * Get taxi services by location
     *
     * @param float $lat
     * @param float $lng
     * @param float $radius
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTaxiServicesByLocation(float $lat, float $lng, float $radius = 10.0): Collection;

    /**
     * Get full taxi service details with relationships
     *
     * @param int $id
     * @return \App\Models\TaxiService
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getFullTaxiServiceDetails(int $id): TaxiService;
}
