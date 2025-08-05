<?php

declare(strict_types=1);

namespace App\Validators;

use App\Models\TaxiService;
use App\Models\VehicleType;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TaxiBookingValidator
{
    public function __construct(
        protected ValidatorFactory $validator
    ) {
    }

    /**
     * Validate taxi booking creation data
     * @throws ValidationException
     */
    public function validateCreate(array $data): array
    {
        $validator = $this->validator->make($data, [
            'taxi_service_id' => [
                'required',
                'integer',
                Rule::exists(TaxiService::class, 'id')
                    ->where('is_active', true)
            ],
            'vehicle_type_id' => [
                'required',
                'integer',
                Rule::exists(VehicleType::class, 'id')
                    ->where('taxi_service_id', $data['taxi_service_id'] ?? null)
            ],
            'pickup_location' => 'required|array:lat,lng',
            'pickup_location.lat' => 'required|numeric|between:-90,90',
            'pickup_location.lng' => 'required|numeric|between:-180,180',
            'dropoff_location' => 'required|array:lat,lng',
            'dropoff_location.lat' => 'required|numeric|between:-90,90',
            'dropoff_location.lng' => 'required|numeric|between:-180,180',
            'pickup_date_time' => 'required|date_format:Y-m-d H:i|after_or_equal:now',
            'type_of_booking' => [
                'required',
                Rule::in(['one_way', 'round_trip', 'hourly'])
            ],
            'return_time' => [
                Rule::requiredIf(fn() => $data['type_of_booking'] === 'round_trip'),
                'date_format:Y-m-d H:i',
                'after:pickup_date_time'
            ],
            'duration_hours' => [
                Rule::requiredIf(fn() => $data['type_of_booking'] === 'hourly'),
                'integer',
                'min:1',
                'max:12'
            ],
            'passenger_count' => 'required|integer|min:1|max:15',
            'special_requests' => 'nullable|string|max:500'
        ], $this->messages(), $this->customAttributes());

        return $validator->validate();
    }

    protected function messages(): array
    {
        return [
            'vehicle_type_id.exists' => __('validation.custom.vehicle_type.invalid_service'),
            'pickup_date_time.after_or_equal' => __('validation.custom.pickup_time.future'),
            'return_time.after' => __('validation.custom.return_time.after_pickup'),
        ];
    }

    protected function customAttributes(): array
    {
        return [
            'taxi_service_id' => __('Taxi Service'),
            'vehicle_type_id' => __('Vehicle Type'),
            'pickup_location.lat' => __('Pickup Latitude'),
            'pickup_location.lng' => __('Pickup Longitude'),
        ];
    }
}
