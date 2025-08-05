<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TravelBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'number_of_people' => 'required|integer|min:1',
            'ticket_type' => 'required|in:one_way,round_trip',
        ];
    }
}
