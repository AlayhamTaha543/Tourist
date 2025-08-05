<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantOrderRequest extends FormRequest
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
            'orderItems' => 'required|array|min:1',
            'orderItems.*.item_id' => 'required|exists:menu_items,id',
            'orderItems.*.quantity' => 'required|integer|min:1',
            'orderItems.*.size' => 'required|string|in:small,medium,large',
        ];
    }
}
