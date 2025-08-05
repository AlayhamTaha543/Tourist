<?php

namespace App\Http\Requests\Api\Trip;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\TripIsPending;
use App\Rules\DriverIsAvailable;
use App\Rules\DriverHasActiveVehicle;
use Illuminate\Support\Facades\Auth;

class AcceptTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        // Only authenticated drivers can accept trips
        // return Auth::check() && request()->user()->hasRole('driver');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $driverId = request()->user()->driver->id ?? null;

        return [
            'trip_id' => [
                'required',
                'integer',
                'exists:trips,id',
                new TripIsPending(),
            ],
            'driver_id' => [
                'sometimes',
                'integer',
                'exists:drivers,id',
                new DriverIsAvailable(),
                new DriverHasActiveVehicle(),
            ],
            'estimated_arrival_minutes' => [
                'sometimes',
                'integer',
                'min:1',
                'max:60'
            ],
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
            'trip_id.required' => 'Trip ID is required',
            'trip_id.exists' => 'Invalid trip ID',
            'driver_id.exists' => 'Invalid driver ID',
            'estimated_arrival_minutes.min' => 'Estimated arrival time must be at least 1 minute',
            'estimated_arrival_minutes.max' => 'Estimated arrival time cannot exceed 60 minutes',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // If driver_id is not provided, use the authenticated driver's ID
        if (!$this->has('driver_id') && Auth::check() && request()->user()->driver) {
            $this->merge([
                'driver_id' => request()->user()->driver->id,
            ]);
        }
    }
}
