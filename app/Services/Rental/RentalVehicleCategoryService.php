<?php

namespace App\Services\Rental;

use App\Repositories\Interfaces\Rent\RentalVehicleCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RentalVehicleCategoryService
{
    public function __construct(
        protected RentalVehicleCategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getPaginatedCategories(int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage);
    }

    public function getCategoryById(int $id): ?object
    {
        return $this->categoryRepository->find($id);
    }

    public function createCategory(array $data): object
    {
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $id, array $data): bool
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

    public function getCategoryVehicles(int $categoryId): Collection
    {
        return $this->categoryRepository->vehicles($categoryId);
    }

    public function getCategoryBySlug(string $slug): ?object
    {
        return $this->categoryRepository->findByName($slug);
    }
}
