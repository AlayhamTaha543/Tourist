<?php

namespace App\Http\Requests\TaxiBooking;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxiBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'taxi_service_id' => 'required|exists:taxi_services,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'pickup_date_time' => 'required|date',
            'pickup_location' => 'required|array',
            'pickup_location.latitude' => 'required|numeric',
            'pickup_location.longitude' => 'required|numeric',
            'pickup_location.name' => 'nullable|string',
            'pickup_location.address' => 'nullable|string',
            'dropoff_location' => 'nullable|array',
            'dropoff_location.latitude' => 'required_with:dropoff_location|numeric',
            'dropoff_location.longitude' => 'required_with:dropoff_location|numeric',
            'dropoff_location.name' => 'nullable|string',
            'dropoff_location.address' => 'nullable|string',
            'radius' => 'nullable|integer|min:0',
            'is_shared' => 'nullable|boolean',
            'passenger_count' => 'nullable|integer|min:1|required_if:is_shared,true',
            'type_of_booking' => 'nullable|in:one_way,round_trip,hourly',
            'promotion_code' => 'nullable|string',
        ];
    }
}