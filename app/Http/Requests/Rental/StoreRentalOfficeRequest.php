<?php

namespace App\Http\Requests\Rental;

use Illuminate\Foundation\Http\FormRequest;

class StoreRentalOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'manager_id' => 'required|exists:admins,id',
        ];
    }
}
