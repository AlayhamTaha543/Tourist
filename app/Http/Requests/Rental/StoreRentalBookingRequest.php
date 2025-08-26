<?php

namespace App\Http\Requests\Rental;

use App\Services\Rental\RentalBookingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRentalBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(RentalBookingService $rentalBookingService): array
    {
        return [
            'vehicle_id' => [
                'required',
                'exists:rental_vehicles,id',
                Rule::prohibitedIf(function () use ($rentalBookingService) {
                    $vehicleId = $this->input('vehicle_id');
                    $pickupDate = $this->input('pickup_date');
                    $returnDate = $this->input('return_date');

                    if ($vehicleId && $pickupDate && $returnDate) {
                        return !$rentalBookingService->isVehicleAvailable($vehicleId, $pickupDate, $returnDate);
                    }
                    return false;
                }),
            ],
            'office_id' => 'required|exists:rental_offices,id',
            'pickup_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:pickup_date',
            'payment_method' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.prohibited_if' => 'vehicle isn\'t available in the selected date',
        ];
    }
}
