<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'TaxiServiceID' => 'required|exists:TaxiServices,TaxiServiceID',
            'VehicleTypeID' => 'required|exists:VehicleTypes,VehicleTypeID',
            'RegistrationNumber' => 'required|string|unique:Vehicles,RegistrationNumber',
            'Model' => 'nullable|string',
            'Year' => 'nullable|integer',
            'Color' => 'nullable|string',
            'IsActive' => 'boolean',
        ];
    }
}
