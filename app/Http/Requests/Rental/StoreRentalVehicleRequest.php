<?php

namespace App\Http\Requests\Rental;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'office_id' => 'required|exists:rental_offices,id',
            'category_id' => 'required|exists:rental_vehicle_categories,id',
            'license_plate' => 'required|string|max:20|unique:rental_vehicles',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'seating_capacity' => 'nullable|integer|min:1',
            'status' => 'nullable|in:available,reserved,in_maintenance',
        ];
    }
}
