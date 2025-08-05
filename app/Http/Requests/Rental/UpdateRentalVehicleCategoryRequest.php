<?php

namespace App\Http\Requests\Rental;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalVehicleCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:50|unique:rental_vehicle_categories,name,' . $this->id,
            'price_per_day' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }
}
