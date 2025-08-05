<?php
namespace App\Http\Requests\Api\Trip;

use Illuminate\Foundation\Http\FormRequest;

class CancelTripRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:trips,id'
        ];
    }
}
