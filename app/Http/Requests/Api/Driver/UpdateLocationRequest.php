<?php

namespace App\Http\Requests\Api\Driver;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidCoordinates;
use Illuminate\Support\Facades\Auth;

class UpdateLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        // Only authenticated drivers can update their location
        // return Auth::check() && request()->user()->hasRole('driver');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'lat' => ['required', 'numeric', 'between:-90,90', new ValidCoordinates],
            'lng' => ['required', 'numeric', 'between:-180,180', new ValidCoordinates],
            'heading' => ['sometimes', 'numeric', 'between:0,359'],
            'speed' => ['sometimes', 'numeric', 'min:0'],
            'accuracy' => ['sometimes', 'numeric', 'min:0'],
            // 'driver_id' => ['sometimes', 'integer', 'exists:drivers,id'],
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
            'lat.required' => 'Latitude is required',
            'lat.between' => 'Latitude must be between -90 and 90',
            'lng.required' => 'Longitude is required',
            'lng.between' => 'Longitude must be between -180 and 180',
            'heading.between' => 'Heading must be between 0 and 359 degrees',
            'speed.min' => 'Speed cannot be negative',
            'accuracy.min' => 'Accuracy cannot be negative',
            'driver_id.exists' => 'Invalid driver ID',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Convert coordinates to float for proper validation
        if ($this->has('lat')) {
            $this->merge([
                'lat' => (float) $this->lat,
            ]);
        }

        if ($this->has('lng')) {
            $this->merge([
                'lng' => (float) $this->lng,
            ]);
        }


        if (!$this->has('driver_id') && Auth::check() && request()->user()->driver) {
            $this->merge([
                'driver_id' => request()->user()->driver->id,
            ]);
        }
    }
}
