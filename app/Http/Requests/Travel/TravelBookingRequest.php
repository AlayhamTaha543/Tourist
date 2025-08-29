<?php

namespace App\Http\Requests\Travel;

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
            'flight_type_name' => 'required|string|exists:flight_types,flight_type',
            'passport_image' => ['required', 'image', 'max:2048', new \App\Rules\ValidatePassportImage()],
            'ticket_type' => 'required|in:one_way,round_trip',
            'number_of_adults' => 'nullable|integer|min:1',
            'schedule_id' => 'nullable|integer|exists:tour_schedules,id',
            'additional_bag_fines' => 'nullable|numeric',
            'discount' => 'nullable|boolean',
        ];
    }


    public function withValidator($validator)
    {
        $validator->sometimes(['number_of_adults', 'schedule_id'], 'required', function ($input) {
            return $input->filled('number_of_adults') || $input->filled('schedule_id');
        });
    }
}


