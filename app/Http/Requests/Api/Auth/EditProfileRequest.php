<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Assuming authenticated users can edit their profile
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fullname' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
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
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image may not be greater than 2MB.',
            'fullname.required' => 'The full name field is required.',
            'fullname.string' => 'The full name must be a string.',
            'fullname.max' => 'The full name may not be greater than 255 characters.',
        ];
    }

    /**
     * Parse the fullname into first_name and last_name
     *
     * @return array
     */
    public function getParsedName(): array
    {
        $fullname = trim($this->input('fullname', ''));
        $nameParts = explode(' ', $fullname, 2); // Split into maximum 2 parts

        return [
            'first_name' => $nameParts[0] ?? '',
            'last_name' => $nameParts[1] ?? '',
        ];
    }
}
