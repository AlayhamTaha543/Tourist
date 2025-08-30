<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CountryDetailsController extends Controller
{
    private function callGeminiApi(string $countryName, string $promptText)
    {
        $geminiApiKey = env('GEMINI_API_KEY');

        if (!$geminiApiKey) {
            return ['error' => 'Gemini API key not configured.', 'status' => 500];
        }

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiApiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $promptText],
                        ],
                    ],
                ],
            ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                return ['data' => $responseData['candidates'][0]['content']['parts'][0]['text'], 'status' => 200];
            } else {
                return ['error' => 'Failed to get information from Gemini API.', 'details' => $responseData, 'status' => $response->status()];
            }
        } catch (\Exception $e) {
            return ['error' => 'An unexpected error occurred.', 'message' => $e->getMessage(), 'status' => 500];
        }
    }

    public function getFamousFoods(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $countryName = $request->input('country');
        $prompt = "List the three most famous foods of {$countryName}. Provide only the names, separated by commas.";
        $result = $this->callGeminiApi($countryName, $prompt);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error'], 'details' => $result['details'] ?? null], $result['status']);
        }

        return response()->json([
            'country' => $countryName,
            'description' => trim($result['data'])
        ]);
    }

    public function getFamousMuseums(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $countryName = $request->input('country');
        $prompt = "List three famous museums in {$countryName}. Provide only the names, separated by commas.";
        $result = $this->callGeminiApi($countryName, $prompt);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error'], 'details' => $result['details'] ?? null], $result['status']);
        }

        return response()->json([
            'country' => $countryName,
            'description' => trim($result['data'])
        ]);
    }

    public function getFamousPublicParks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $countryName = $request->input('country');
        $prompt = "List three famous public parks in {$countryName}. Provide only the names, separated by commas.";
        $result = $this->callGeminiApi($countryName, $prompt);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error'], 'details' => $result['details'] ?? null], $result['status']);
        }

        return response()->json([
            'country' => $countryName,
            'description' => trim($result['data'])
        ]);
    }

    public function getFamousShoppingMalls(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $countryName = $request->input('country');
        $prompt = "List three famous shopping malls in {$countryName}. Provide only the names, separated by commas.";
        $result = $this->callGeminiApi($countryName, $prompt);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error'], 'details' => $result['details'] ?? null], $result['status']);
        }

        return response()->json([
            'country' => $countryName,
            'description' => trim($result['data'])
        ]);
    }
}
