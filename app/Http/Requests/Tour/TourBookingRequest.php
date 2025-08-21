<?php

namespace App\Http\Requests\Tour;

use Illuminate\Foundation\Http\FormRequest;

class TourBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'number_of_adults' => 'required|integer|min:0',
            'number_of_children' => 'required|integer|min:0',
            'schedule_id' => 'required|integer|exists:tour_schedules,id',
            'promotion_code' => 'nullable|string',
        ];
    }
}
