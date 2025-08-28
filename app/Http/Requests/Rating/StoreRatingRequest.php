<?php

namespace App\Http\Requests\Rating;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\{Hotel, TravelAgency, TaxiService, RentalOffice, Restaurant, Tour, Booking, Application};

class StoreRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust authorization logic as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rateable_type' => [
                'required',
                'string',
                Rule::in(['Hotel', 'TravelAgency', 'TaxiService', 'RentalOffice', 'Restaurant', 'Tour', 'Application']),
            ],
            'booking_id' => [
                'nullable', // Allow null for application ratings
                'integer',
                Rule::requiredIf($this->input('rateable_type') !== 'Application'),
                Rule::when(
                    $this->input('booking_id') !== null && $this->input('rateable_type') !== 'Application',
                    [
                        Rule::exists('bookings', 'id')->where(function ($query) {
                            return $query->where('user_id', $this->user()->id);
                        }),
                    ]
                ),
            ],
            'rateable_id' => [
                'required',
                'integer',
                Rule::when($this->input('rateable_type') === 'Application', ['in:1']), // Assuming '1' is the ID for the application
            ],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $rateableType = $this->rateable_type;
        if ($rateableType === 'Application') {
            $this->merge([
                'rateable_type' => 'App\\Models\\Application', // Or a specific model for the app if it exists
                'rateable_id' => 1, // Fixed ID for the application
                'booking_id' => null, // No booking ID for app ratings
            ]);
        } else {
            $this->merge([
                'rateable_type' => 'App\\Models\\' . $rateableType,
            ]);
        }
    }
}