<?php
// ShowTaxiServiceRequest.php
namespace App\Http\Requests\Taxi;

use Illuminate\Foundation\Http\FormRequest;

class ShowTaxiServiceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:taxi_services,id'
        ];
    }
}

//
