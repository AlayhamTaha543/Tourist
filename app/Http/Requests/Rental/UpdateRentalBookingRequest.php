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
            'pickup_date' => 'sometimes|date|after_or_equal:today',
            'return_date' => 'sometimes|date|after:pickup_date',
            'status' => 'sometimes|in:reserved,active,completed,cancelled',
        ];
    }
}
