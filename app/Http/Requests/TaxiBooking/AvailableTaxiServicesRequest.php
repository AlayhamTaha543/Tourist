<?php
namespace App\Http\Requests\TaxiBooking;

use Illuminate\Foundation\Http\FormRequest;

class AvailableTaxiServicesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ];
    }
}