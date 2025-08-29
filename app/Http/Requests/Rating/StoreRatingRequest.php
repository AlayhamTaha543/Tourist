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
                Rule::in([
                    'App\\Models\\Hotel',
                    'App\\Models\\TravelAgency',
                    'App\\Models\\TaxiService',
                    'App\\Models\\RentalOffice',
                    'App\\Models\\Restaurant',
                    'App\\Models\\Tour',
                    'App\\Models\\Application',
                ]),
            ],
            'booking_id' => [
                'nullable',
                'integer',
                // Conditionally apply 'required' and 'exists' rules
                $this->input('rateable_type') !== 'App\\Models\\Application' ? 'required' : null,
                Rule::when(
                    $this->input('rateable_type') !== 'App\\Models\\Application' && $this->input('booking_id') !== null,
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
                Rule::when($this->input('rateable_type') === 'App\\Models\\Application', ['in:1']),
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
        // No longer need to prepend 'App\\Models\\' here as rules now expect FQCN
        // Also, booking_id is handled by rules directly now.
        if ($this->rateable_type === 'Application') {
            $this->merge([
                'rateable_type' => 'App\\Models\\Application',
                'rateable_id' => 1,
                'booking_id' => null,
            ]);
        } else {
            $this->merge([
                'rateable_type' => 'App\\Models\\' . $this->rateable_type,
            ]);
        }
    }
}
