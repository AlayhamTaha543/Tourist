<?php
namespace App\Http\Requests\Api\Trip;

use Illuminate\Foundation\Http\FormRequest;

class NearbyTripsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'sometimes|integer|min:1'
        ];
    }
}
