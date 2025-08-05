<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'error' => [
                'code' => $this->resource['code'] ?? 500,
                'message' => $this->resource['message'] ?? 'An error occurred',
                'details' => $this->when(isset($this->resource['details']), fn() => $this->resource['details']),
                'validation_errors' => $this->when(isset($this->resource['validation_errors']),
                    fn() => $this->resource['validation_errors']),
            ],
            'request_id' => request()->header('X-Request-ID') ?? uniqid(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $statusCode = $this->resource['code'] ?? 500;
        $response->setStatusCode($statusCode);
    }
}
