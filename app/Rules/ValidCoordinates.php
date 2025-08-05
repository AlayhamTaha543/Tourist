<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCoordinates implements ValidationRule
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
        // Check if the value is a valid float
        if (!is_numeric($value)) {
            $fail("The {$attribute} must be a valid coordinate.");
            return;
        }

        // Check if the value is within valid coordinate range
        if ($attribute === 'lat' || str_ends_with($attribute, '_lat')) {
            if ($value < -90 || $value > 90) {
                $fail("The {$attribute} must be between -90 and 90.");
            }
        } elseif ($attribute === 'lng' || str_ends_with($attribute, '_lng')) {
            if ($value < -180 || $value > 180) {
                $fail("The {$attribute} must be between -180 and 180.");
            }
        }
    }
}
