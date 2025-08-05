<?php
namespace App\Http\Requests\TaxiBooking;

use Illuminate\Foundation\Http\FormRequest;

class ShowAllTaxiBookingRequest extends FormRequest
{
    public function rules()
    {
        return [
            // 'id' => 'required|integer|exists:users,id'
        ];
    }
}
