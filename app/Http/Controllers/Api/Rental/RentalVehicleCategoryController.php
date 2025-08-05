<?php

namespace App\Http\Controllers\Api\Rental;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Rental\StoreRentalVehicleCategoryRequest;
use App\Http\Requests\Rental\UpdateRentalVehicleCategoryRequest;
use App\Services\Rental\RentalVehicleCategoryService;
use Illuminate\Http\JsonResponse;

class RentalVehicleCategoryController extends BaseController
{
    protected $categoryService;

    public function __construct(RentalVehicleCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getPaginatedCategories();
            return $this->successResponse($categories);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreRentalVehicleCategoryRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $category = $this->categoryService->createCategory($validated);
            return $this->successResponse($category, 'Category created successfully', 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryById($id);

            if (!$category) {
                return $this->resourceNotFound('Vehicle category');
            }

            return $this->successResponse($category);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateRentalVehicleCategoryRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (!$this->categoryService->updateCategory($id, $validated)) {
                return $this->resourceNotFound('Vehicle category');
            }

            $updatedCategory = $this->categoryService->getCategoryById($id);
            return $this->successResponse($updatedCategory, 'Category updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            if (!$this->categoryService->deleteCategory($id)) {
                return $this->resourceNotFound('Vehicle category');
            }

            return $this->successResponse(null, 'Category deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getCategoryVehicles(int $id): JsonResponse
    {
        try {
            $vehicles = $this->categoryService->getCategoryVehicles($id);
            return $this->successResponse($vehicles);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
