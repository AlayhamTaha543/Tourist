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
        Log::info('ValidatePassportImage rule started for attribute: ' . $attribute);

        if (!$value->isValid()) {
            $fail('The :attribute is not a valid uploaded file.');
            Log::warning('Uploaded file is not valid for attribute: ' . $attribute);
            return;
        }

        $apiEndpoint = env('GEMINI_API_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/');
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_PASSPORT_MODEL', 'gemini-2.5-flash'); // Using gemini-1.5-flash as default

        Log::info("LLM API Configuration: Endpoint={$apiEndpoint}, Model={$model}");

        if (!$apiKey) {
            Log::error('GEMINI_API_KEY is not set in .env file. Passport validation service is not configured.');
            $fail('Passport validation service is not configured.');
            return;
        }

        try {
            // Convert image to base64 for API submission
            $base64Image = base64_encode($value->get());
            $mimeType = $value->getMimeType();
            Log::info('Image converted to base64 with MIME type: ' . $mimeType);

            $requestPayload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => 'Does this image clearly show a passport? Respond with "yes" or "no" and a brief explanation.'
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $base64Image
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            Log::info('Sending request to Gemini API with base64 image payload.');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$apiEndpoint}{$model}:generateContent?key={$apiKey}", $requestPayload);

            Log::info('Received response from Gemini API. Status: ' . $response->status() . ', Body: ' . $response->body());

            if ($response->failed()) {
                Log::error('Gemini API request failed: ' . $response->body());
                $fail('Failed to verify passport image with external service. Please try again.');
                return;
            }

            $responseData = $response->json();
            $llmResponseContent = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
            Log::info('LLM Response Content: ' . $llmResponseContent);

            if (stripos($llmResponseContent, 'yes') === false) {
                Log::warning('LLM analysis determined the image is NOT a passport.');
                $fail('The uploaded file does not appear to be a valid passport image according to the AI analysis.');
            } else {
                Log::info('LLM analysis determined the image IS a passport. Validation passed.');
            }

        } catch (\Exception $e) {
            Log::error('Error calling Gemini API: ' . $e->getMessage());
            $fail('An error occurred during passport image verification. Please try again.');
        } finally {
            Log::info('ValidatePassportImage rule finished.');
        }
    }
}