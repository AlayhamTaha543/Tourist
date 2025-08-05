<?php

declare(strict_types=1);

namespace App\Validators;

use App\Models\VehicleType;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SharedBookingValidator extends TaxiBookingValidator
{
    public function validateCreate(array $data): array
    {
        $baseData = parent::validateCreate($data);

        $validator = $this->validator->make($data, [
            'is_shared' => 'required|boolean',
            'max_additional_passengers' => [
                'required_if:is_shared,true',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($data) {
                    $vehicleType = VehicleType::find($data['vehicle_type_id']);
                    if ($vehicleType && ($value + $data['passenger_count']) > $vehicleType->max_capacity) {
                        $fail(__('validation.custom.shared_ride.over_capacity'));
                    }
                }
            ],
            'preferred_gender' => [
                'nullable',
                Rule::in(['male', 'female', 'none'])
            ],
            'allow_pets' => 'boolean',
            'allow_luggage' => 'boolean',
            'shared_pickup_window' => [
                'required_if:is_shared,true',
                'integer',
                'min:15',
                'max:60'
            ]
        ], $this->sharedMessages(), $this->sharedAttributes());

        return array_merge($baseData, $validator->validate());
    }

    protected function sharedMessages(): array
    {
        return [
            'max_additional_passengers.required_if' => __('validation.custom.shared_ride.max_passengers_required'),
            'shared_pickup_window.required_if' => __('validation.custom.shared_ride.pickup_window_required'),
        ];
    }

    protected function sharedAttributes(): array
    {
        return [
            'max_additional_passengers' => __('Max Additional Passengers'),
            'shared_pickup_window' => __('Pickup Window'),
        ];
    }
}
