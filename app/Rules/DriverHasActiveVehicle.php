<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Driver;

class DriverHasActiveVehicle implements ValidationRule
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
        $driver = Driver::find($value);

        if (!$driver) {
            $fail('The driver does not exist.');
            return;
        }

        // Check if driver has an active vehicle assignment
        $hasActiveVehicle = $driver->vehicleAssignments()
            ->whereNull('unassigned_at')
            ->exists();

        if (!$hasActiveVehicle) {
            $fail('The driver does not have an assigned vehicle.');
        }
    }
}
