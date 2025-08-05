<?php
namespace App\Http\Requests\Taxi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxiServiceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:taxi_services,id',
            // Include other validation rules from your original TaxiServiceRequest
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            // Add other fields as needed
        ];
    }
}
