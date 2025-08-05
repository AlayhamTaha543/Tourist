<?php
namespace App\Repositories\Interfaces\Taxi;

interface TaxiServiceRepositoryInterface
{
    public function all(bool $withRelations = false);
    public function find(int $id, bool $withRelations = false);
    public function paginate(int $perPage = 15);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function updateRating(int $id, float $newRating);
    public function getByLocation(int $locationId, bool $activeOnly = true);
}
