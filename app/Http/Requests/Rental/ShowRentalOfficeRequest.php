<?php
namespace App\Http\Requests\Rental;

use Illuminate\Foundation\Http\FormRequest;

class ShowRentalOfficeRequest extends FormRequest
{

    public function rules()
    {
        return [
            'id' => 'required|integer|exists:taxi_services,id'
        ];
    }
}

//
