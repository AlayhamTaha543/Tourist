<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ValidatePassportImage implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value->isValid()) {
            $fail('The :attribute is not a valid uploaded file.');
            return;
        }

        // Convert image to base64 for API submission
        $base64Image = base64_encode($value->get());

        // TODO: Replace with your actual LLM API endpoint
        $apiEndpoint = env('LLM_PASSPORT_API_ENDPOINT', 'http://your-llm-api.com/analyze-passport');
        // TODO: Replace with your actual API key environment variable name
        $apiKey = env('LLM_PASSPORT_API_KEY');

        if (!$apiKey) {
            Log::error('LLM_PASSPORT_API_KEY is not set in .env file.');
            $fail('Passport validation service is not configured.');
            return;
        }

        try {
            $response = Http::withHeaders([
                // TODO: Add any other required headers, e.g., 'Content-Type': 'application/json'
                'Authorization' => 'Bearer ' . $apiKey, // Example for Bearer token authentication
            ])->post($apiEndpoint, [
                // TODO: Adjust payload based on your LLM API's expected request format
                'image' => $base64Image,
                'filename' => $value->getClientOriginalName(),
            ]);

            if ($response->failed()) {
                Log::error('LLM Passport API request failed: ' . $response->body());
                $fail('Failed to verify passport image with external service. Please try again.');
                return;
            }

            $responseData = $response->json();

            // TODO: Adjust this logic based on your LLM API's expected response format
            // Example: Assuming the API returns a 'is_passport' boolean and a 'confidence' score
            if (!isset($responseData['is_passport']) || !$responseData['is_passport']) {
                $fail('The uploaded file does not appear to be a valid passport image.');
                return;
            }

            // Optional: Check confidence score if your API provides one
            // if (isset($responseData['confidence']) && $responseData['confidence'] < 0.8) {
            //     $fail('The uploaded file is a passport image, but with low confidence. Please try again with a clearer image.');
            //     return;
            // }

        } catch (\Exception $e) {
            Log::error('Error calling LLM Passport API: ' . $e->getMessage());
            $fail('An error occurred during passport image verification. Please try again.');
        }
    }
}
