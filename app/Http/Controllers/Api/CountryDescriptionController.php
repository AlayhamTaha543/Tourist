<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CountryDescriptionController extends Controller
{
    public function getDescription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $countryName = $request->input('country');
        $geminiApiKey = env('GEMINI_API_KEY');

        if (!$geminiApiKey) {
            return response()->json(['error' => 'Gemini API key not configured.'], 500);
        }

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiApiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "Provide a small, concise description of the country: {$countryName}. Limit it to 2-3 sentences."],
                        ],
                    ],
                ],
            ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $description = $responseData['candidates'][0]['content']['parts'][0]['text'];
                return response()->json(['country' => $countryName, 'description' => $description]);
            } else {
                return response()->json(['error' => 'Failed to get description from Gemini API.', 'details' => $responseData], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}
