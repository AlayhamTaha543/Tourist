<?php
namespace App\Http\Requests\Api\Trip;

use Illuminate\Foundation\Http\FormRequest;

class CompleteTripRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:trips,id',
            'distance' => 'required|numeric|min:0',
            'additional_data' => 'sometimes|array'
        ];
    }
}
