<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'image' => 'nullable|image|max:2048', // 2MB in kilobytes (2048KB)
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'location' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ];
    }
}
