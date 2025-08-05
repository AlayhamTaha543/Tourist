<?php

namespace App\Http\Requests\Rental;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:100',
            'address' => 'nullable|string|max:255',
            'location_id' => 'sometimes|exists:locations,id',
            'manager_id' => 'sometimes|exists:admins,id',
        ];
    }
}
