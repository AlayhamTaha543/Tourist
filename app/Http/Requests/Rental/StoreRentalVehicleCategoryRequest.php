<?php

namespace App\Http\Requests\Rental;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalVehicleCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:rental_vehicle_categories,name',
            'price_per_day' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }
}
