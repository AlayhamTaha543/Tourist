<?php

namespace App\Http\Requests\Api\Trip;

use App\Models\Driver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): void
    {
        // Only authenticated users with tourist role can create trips
        // return Auth::check() && request()->user()->hasRole('tourist');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'taxi_service_id' => ['required', 'integer', 'exists:taxi_services,id,is_active,1'],
            'vehicle_type_id' => [
                'required',
                'integer',
                'exists:vehicle_types,id,is_active,1',
                Rule::exists('vehicle_types')->where(function ($query) {
                    $query->where('taxi_service_id', $this->input('taxi_service_id'));
                }),
            ],
            'pickup_lat' => ['required', 'numeric', 'between:-90,90'],
            'pickup_lng' => ['required', 'numeric', 'between:-180,180'],
            'dropoff_lat' => ['required', 'numeric', 'between:-90,90'],
            'dropoff_lng' => ['required', 'numeric', 'between:-180,180'],
            'scheduled_time' => ['nullable', 'date', 'after:now'],
            'trip_type' => ['required', Rule::in(['solo', 'pool'])],
            'passenger_count' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'taxi_service_id.exists' => 'The selected taxi service is not available.',
            'vehicle_type_id.exists' => 'The selected vehicle type is not available for this taxi service.',
            'pickup_lat.between' => 'The pickup latitude must be a valid coordinate.',
            'pickup_lng.between' => 'The pickup longitude must be a valid coordinate.',
            'dropoff_lat.between' => 'The dropoff latitude must be a valid coordinate.',
            'dropoff_lng.between' => 'The dropoff longitude must be a valid coordinate.',
            'scheduled_time.after' => 'The scheduled time must be in the future.',
            'passenger_count.min' => 'At least one passenger is required.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert string coordinates to float if needed
        if ($this->has('pickup_lat') && is_string($this->pickup_lat)) {
            $this->merge([
                'pickup_lat' => (float) $this->pickup_lat,
            ]);
        }

        if ($this->has('pickup_lng') && is_string($this->pickup_lng)) {
            $this->merge([
                'pickup_lng' => (float) $this->pickup_lng,
            ]);
        }

        if ($this->has('dropoff_lat') && is_string($this->dropoff_lat)) {
            $this->merge([
                'dropoff_lat' => (float) $this->dropoff_lat,
            ]);
        }

        if ($this->has('dropoff_lng') && is_string($this->dropoff_lng)) {
            $this->merge([
                'dropoff_lng' => (float) $this->dropoff_lng,
            ]);
        }
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $data = parent::validationData();

        // Add user_id to the data
        $data['user_id'] = Auth::id();

        return $data;
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if the vehicle type can accommodate the passenger count
            if ($this->has('vehicle_type_id') && $this->has('passenger_count')) {
                $vehicleType = \App\Models\VehicleType::find($this->vehicle_type_id);
                if ($vehicleType && $this->passenger_count > $vehicleType->max_passengers) {
                    $validator->errors()->add(
                        'passenger_count',
                        "The selected vehicle type can only accommodate {$vehicleType->max_passengers} passengers."
                    );
                }
            }

            // Check if there are available drivers for this service and vehicle type
            if ($this->has('taxi_service_id') && $this->has('vehicle_type_id')) {
                $availableDrivers = Driver::where('taxi_service_id', $this->taxi_service_id)
                    ->where('availability_status', 'available')
                    ->where('is_active', true)
                    ->whereHas('activeVehicle', function ($query) {
                        $query->where('vehicle_type_id', $this->vehicle_type_id);
                    })
                    ->count();

                if ($availableDrivers === 0) {
                    $validator->errors()->add(
                        'vehicle_type_id',
                        'No drivers are currently available for this vehicle type. Please try another option.'
                    );
                }
            }
        });
    }
}
