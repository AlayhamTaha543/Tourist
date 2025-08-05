<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\TaxiService;

class TaxiServiceExists implements ValidationRule
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
        $taxiService = TaxiService::find($value);

        if (!$taxiService) {
            $fail('The selected taxi service does not exist.');
            return;
        }

        if (!$taxiService->is_active) {
            $fail('The selected taxi service is not currently active.');
        }
    }
}
