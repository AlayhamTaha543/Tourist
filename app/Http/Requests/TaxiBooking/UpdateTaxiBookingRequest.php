<?php

namespace App\Http\Requests\TaxiBooking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxiBookingRequest extends FormRequest
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
            'booking_id' => 'exists:bookings,id',
            'taxi_service_id' => 'exists:taxi_services,id',
            'vehicle_type_id' => 'exists:vehicle_types,id',
            'pickup_address' => 'string',
            'dropoff_address' => 'string',
            'pickup_location_id' => 'exists:locations,id',
            'dropoff_location_id' => 'exists:locations,id',
            'pickup_date_time' => 'date',
            'driver_id' => 'nullable|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'is_shared' => 'nullable|boolean',
            'passenger_count' => 'nullable|integer|min:1',
            'max_additional_passengers' => 'nullable|integer|min:0',
            'type_of_booking' => 'nullable|in:one_way,round_trip,hourly',
            'status' => 'nullable|in:pending,confirmed,cancelled,completed',
            // For nested location data
            'pickup_location' => 'nullable|array',
            'pickup_location.latitude' => 'required_with:pickup_location|numeric',
            'pickup_location.longitude' => 'required_with:pickup_location|numeric',
            'pickup_location.name' => 'nullable|string',
            'pickup_location.address' => 'nullable|string',
            'pickup_location.city_id' => 'nullable|exists:cities,id',
            'dropoff_location' => 'nullable|array',
            'dropoff_location.latitude' => 'required_with:dropoff_location|numeric',
            'dropoff_location.longitude' => 'required_with:dropoff_location|numeric',
            'dropoff_location.name' => 'nullable|string',
            'dropoff_location.address' => 'nullable|string',
            'dropoff_location.city_id' => 'nullable|exists:cities,id',
        ];
    }
}
