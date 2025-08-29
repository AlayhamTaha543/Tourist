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

        $apiEndpoint = env('LLM_PASSPORT_API_ENDPOINT', 'https://openrouter.ai/api/v1/chat/completions');
        $apiKey = env('OPENROUTER_API_KEY');
        $model = env('LLM_PASSPORT_MODEL', 'mistralai/mistral-small-3.2-24b-instruct:free');

        Log::info("LLM API Configuration: Endpoint={$apiEndpoint}, Model={$model}");

        if (!$apiKey) {
            Log::error('OPENROUTER_API_KEY is not set in .env file. Passport validation service is not configured.');
            $fail('Passport validation service is not configured.');
            return;
        }

        try {
            // Convert image to base64 for API submission
            $base64Image = base64_encode($value->get());
            $mimeType = $value->getMimeType();
            Log::info('Image converted to base64 with MIME type: ' . $mimeType);

            $requestPayload = [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Does this image clearly show a passport? Respond with "yes" or "no" and a brief explanation.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$base64Image}"
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            Log::info('Sending request to OpenRouter API with base64 image payload.');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post($apiEndpoint, $requestPayload);

            Log::info('Received response from OpenRouter API. Status: ' . $response->status() . ', Body: ' . $response->body());

            if ($response->failed()) {
                Log::error('OpenRouter API request failed: ' . $response->body());
                $fail('Failed to verify passport image with external service. Please try again.');
                return;
            }

            $responseData = $response->json();
            $llmResponseContent = $responseData['choices'][0]['message']['content'] ?? '';
            Log::info('LLM Response Content: ' . $llmResponseContent);

            if (stripos($llmResponseContent, 'yes') === false) {
                Log::warning('LLM analysis determined the image is NOT a passport.');
                $fail('The uploaded file does not appear to be a valid passport image according to the AI analysis.');
            } else {
                Log::info('LLM analysis determined the image IS a passport. Validation passed.');
            }

        } catch (\Exception $e) {
            Log::error('Error calling OpenRouter API: ' . $e->getMessage());
            $fail('An error occurred during passport image verification. Please try again.');
        } finally {
            Log::info('ValidatePassportImage rule finished.');
        }
    }
}
