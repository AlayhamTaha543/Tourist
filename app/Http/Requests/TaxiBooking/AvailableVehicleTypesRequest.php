<?php
namespace App\Http\Requests\TaxiBooking;

use Illuminate\Foundation\Http\FormRequest;

class AvailableVehicleTypesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'taxi_service_id' => 'required|integer|exists:taxi_services,id'
        ];
    }
}
