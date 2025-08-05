<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
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
        $vehicleId = $this->route('vehicle');

        return [
            'TaxiServiceID' => 'exists:TaxiServices,TaxiServiceID',
            'VehicleTypeID' => 'exists:VehicleTypes,VehicleTypeID',
            'RegistrationNumber' => 'string|unique:Vehicles,RegistrationNumber,' . $vehicleId . ',VehicleID',
            'Model' => 'nullable|string',
            'Year' => 'nullable|integer',
            'Color' => 'nullable|string',
            'IsActive' => 'boolean',
        ];
    }
}
