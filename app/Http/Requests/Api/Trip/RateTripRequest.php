<?php

namespace App\Http\Requests\Api\Trip;

use App\Models\Rating;
use App\Models\Trip;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RateTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        // Only authenticated users who completed the trip can rate
        $trip = Trip::find($this->route('trip'));

        return $trip &&
            $this->user() &&
            $trip->getAttribute('user_id') === $this->user()->id &&
            $trip->getAttribute('status') === 'completed';
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        return [
            'rating' => [
                'required',
                'numeric',
                'between:1,5',
                $this->uniqueRatingRule()
            ],
            'comment' => 'nullable|string|max:500'
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'rating.unique' => 'You have already rated this trip.',
            'rating.between' => 'Rating must be between :min and :max stars.',
            'comment.max' => 'Comment cannot exceed :max characters.'
        ];
    }



    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'trip_id' => $this->route('trip')
        ]);
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $trip = Trip::find($this->route('trip'));

            if (!$trip) {
                $validator->errors()->add('trip', 'Invalid trip specified.');
                return;
            }

            if ($trip->status !== 'completed') {
                $validator->errors()->add(
                    'trip',
                    'You can only rate completed trips.'
                );
            }

            if ($trip->user_id !== $this->user()->id) {
                $validator->errors()->add(
                    'trip',
                    'You can only rate trips you participated in.'
                );
            }
        });
    }
}
