<?php

namespace App\Http\Requests\Admin\Trip;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidCoordinates;
use App\Rules\DriverIsAvailable;
use Illuminate\Support\Facades\Auth;

class UpdateTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     // Only admin users can update trips
    //     // return Auth::check() && Auth::user()->hasRole('admin');
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'driver_id' => [
                'sometimes',
                'integer',
                'exists:drivers,id',
                new DriverIsAvailable(),
            ],
            'vehicle_id' => [
                'sometimes',
                'integer',
                'exists:vehicles,id',
            ],
            'status' => [
                'sometimes',
                Rule::in(['pending', 'accepted', 'in_progress', 'completed', 'cancelled']),
            ],
            'pickup_lat' => [
                'sometimes',
                'numeric',
                'between:-90,90',
                new ValidCoordinates,
            ],
            'pickup_lng' => [
                'sometimes',
                'numeric',
                'between:-180,180',
                new ValidCoordinates,
            ],
            'dropoff_lat' => [
                'sometimes',
                'numeric',
                'between:-90,90',
                new ValidCoordinates,
            ],
            'dropoff_lng' => [
                'sometimes',
                'numeric',
                'between:-180,180',
                new ValidCoordinates,
            ],
            'fare' => ['sometimes', 'numeric', 'min:0'],
            'distance_km' => ['sometimes', 'numeric', 'min:0'],
            'surge_multiplier' => ['sometimes', 'numeric', 'min:1', 'max:5'],
            'trip_type' => ['sometimes', Rule::in(['solo', 'pool'])],
            'requested_at' => ['sometimes', 'date'],
            'started_at' => ['sometimes', 'date', 'nullable'],
            'completed_at' => ['sometimes', 'date', 'nullable'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'driver_id.exists' => 'Invalid driver ID',
            'vehicle_id.exists' => 'Invalid vehicle ID',
            'status.in' => 'Invalid status value',
            'pickup_lat.between' => 'Pickup latitude must be between -90 and 90',
            'pickup_lng.between' => 'Pickup longitude must be between -180 and 180',
            'dropoff_lat.between' => 'Dropoff latitude must be between -90 and 90',
            'dropoff_lng.between' => 'Dropoff longitude must be between -180 and 180',
            'fare.min' => 'Fare cannot be negative',
            'distance_km.min' => 'Distance cannot be negative',
            'surge_multiplier.min' => 'Surge multiplier must be at least 1',
            'surge_multiplier.max' => 'Surge multiplier cannot exceed 5',
            'trip_type.in' => 'Trip type must be either solo or pool',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        // Convert coordinates to float for proper validation
        if ($this->has('pickup_lat')) {
            $data['pickup_lat'] = (float) $this->pickup_lat;
        }

        if ($this->has('pickup_lng')) {
            $data['pickup_lng'] = (float) $this->pickup_lng;
        }

        if ($this->has('dropoff_lat')) {
            $data['dropoff_lat'] = (float) $this->dropoff_lat;
        }

        if ($this->has('dropoff_lng')) {
            $data['dropoff_lng'] = (float) $this->dropoff_lng;
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }
}