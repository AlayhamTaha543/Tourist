<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\VehicleType;

class VehicleTypeExists implements ValidationRule
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
        $vehicleType = VehicleType::find($value);

        if (!$vehicleType) {
            $fail('The selected vehicle type does not exist.');
            return;
        }

        if (!$vehicleType->is_active) {
            $fail('The selected vehicle type is not currently active.');
        }
    }
}
