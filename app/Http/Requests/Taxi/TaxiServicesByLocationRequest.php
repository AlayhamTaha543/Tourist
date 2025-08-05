<?php
// TaxiServicesByLocationRequest.php
namespace App\Http\Requests\Taxi;

use Illuminate\Foundation\Http\FormRequest;

class TaxiServicesByLocationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'location_id' => 'required|integer|exists:locations,id'
        ];
    }
}
