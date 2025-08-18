<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class FeedBackRequest extends FormRequest
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
            'feedback_text' => 'required|string',
            'feedbackable_type' => 'required|string',
            'feedbackable_id' => 'required|integer',
        ];
    }
}
