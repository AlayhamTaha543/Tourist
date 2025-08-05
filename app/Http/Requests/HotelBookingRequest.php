<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelBookingRequest extends FormRequest
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
            'room_type_id'=>'required',
            'check_in_date'=>'after_or_equal:today',
            'number_of_rooms'=>'required',
            'number_of_guests'=>'required',
            'number_of_days' => 'required|integer|min:1'
        ];
    }
}
