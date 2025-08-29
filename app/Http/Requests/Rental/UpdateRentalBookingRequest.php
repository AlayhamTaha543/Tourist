<?php

namespace App\Http\Requests\Rental;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_date' => 'sometimes|date|date_format:Y-m-d|after_or_equal:today',
            'return_date' => 'sometimes|date|date_format:Y-m-d|after:pickup_date',
            'status' => 'sometimes|in:reserved,active,completed,cancelled',
        ];
    }
}
