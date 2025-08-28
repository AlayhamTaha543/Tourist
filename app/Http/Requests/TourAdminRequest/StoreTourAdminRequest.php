<?php

namespace App\Http\Requests\TourAdminRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourAdminRequest extends FormRequest
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
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tour_admin_requests'],
            'age' => ['required', 'integer', 'min:18'],
            'skills' => ['required', 'array'],
            'skills.*' => ['string', 'max:255'], // Validate each skill in the array
            'personal_image' => ['required', 'image', 'max:2048'], // Max 2MB
            'certificate_image' => ['required', 'image', 'max:2048'], // Max 2MB
        ];
    }
}