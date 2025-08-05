<?php
namespace App\Http\Requests\Api\Trip;

use Illuminate\Foundation\Http\FormRequest;

class StartTripRequest extends FormRequest
{
    public function rules()
    {
        return [
            'trip_id' => 'required|integer|exists:trips,id'
        ];
    }
}
