<?php
namespace App\Http\Requests\TaxiBooking;

use Illuminate\Foundation\Http\FormRequest;

class ShowTaxiBookingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:taxi_bookings,id'
        ];
    }
}
