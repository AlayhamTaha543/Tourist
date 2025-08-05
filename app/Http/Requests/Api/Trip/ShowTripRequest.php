<?php

namespace App\Http\Requests\Api\Trip;

use Illuminate\Foundation\Http\FormRequest;

class ShowTripRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:trips,id'
        ];
    }
}