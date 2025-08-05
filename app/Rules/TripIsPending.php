<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Trip;

class TripIsPending implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $trip = Trip::find($value);

        if (!$trip) {
            $fail('The trip does not exist.');
            return;
        }

        if ($trip->status !== 'pending') {
            $fail('The trip is no longer available for acceptance.');
        }
    }
}
