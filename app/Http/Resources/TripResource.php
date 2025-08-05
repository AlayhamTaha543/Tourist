<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Extract coordinates from POINT data
        $pickupCoordinates = $this->extractCoordinates($this->pickup_location);
        $dropoffCoordinates = $this->extractCoordinates($this->dropoff_location);

        return [
            'id' => $this->id,
            'status' => $this->status,
            'trip_type' => $this->trip_type,
            'pickup' => [
                'lat' => $pickupCoordinates['lat'],
                'lng' => $pickupCoordinates['lng'],
            ],
            'dropoff' => [
                'lat' => $dropoffCoordinates['lat'],
                'lng' => $dropoffCoordinates['lng'],
            ],
            'distance_km' => $this->distance_km,
            'fare' => $this->when($this->fare, function () {
                return [
                    'amount' => $this->fare,
                    'currency' => 'USD', // This could be dynamic based on location
                    'surge_multiplier' => $this->surge_multiplier,
                ];
            }),
            'timestamps' => [
                'requested_at' => $this->formatTimestamp($this->requested_at),
                'started_at' => $this->when($this->started_at, fn() => $this->formatTimestamp($this->started_at)),
                'completed_at' => $this->when($this->completed_at, fn() => $this->formatTimestamp($this->completed_at)),
                'created_at' => $this->formatTimestamp($this->created_at),
                'updated_at' => $this->formatTimestamp($this->updated_at),
            ],
            'driver' => $this->when($this->driver_id, function () {
                return new DriverResource($this->whenLoaded('driver'));
            }),
            'vehicle' => $this->when($this->vehicle_id, function () {
                return new VehicleResource($this->whenLoaded('vehicle'));
            }),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            '_links' => [
                'self' => [
                    'href' => route('api.trips.show', $this->id),
                ],
                'cancel' => $this->when(in_array($this->status, ['pending', 'accepted']), [
                    'href' => route('api.trips.cancel', $this->id),
                    'method' => 'POST',
                ]),
                'accept' => $this->when($this->status === 'pending' && $request->user(), [
                    'href' => route('api.trips.accept', $this->id),
                    'method' => 'POST',
                ]),
                // && $request->user()->hasRole('driver')
                'start' => $this->when($this->status === 'accepted' && $request->user(), [
                    'href' => route('api.trips.start', $this->id),
                    'method' => 'POST',
                ]),
                'complete' => $this->when($this->status === 'in_progress' && $request->user(), [
                    'href' => route('api.trips.complete', $this->id),
                    'method' => 'POST',
                ]),
            ],
        ];
    }

    /**
     * Extract lat/lng from POINT data
     *
     * @param mixed $point
     * @return array
     */
    protected function extractCoordinates($point): array
    {
        // Default values
        $lat = 0;
        $lng = 0;

        // If we have a valid point object from MySQL spatial data
        if ($point) {
            // For MySQL 8 spatial data
            if (method_exists($point, 'getLat') && method_exists($point, 'getLng')) {
                $lat = $point->getLat();
                $lng = $point->getLng();
            }
            // For raw data or other formats
            else if (is_string($point)) {
                // Parse POINT format: POINT(lng lat)
                preg_match('/POINT\(([^ ]+) ([^)]+)\)/i', $point, $matches);
                if (count($matches) === 3) {
                    $lng = (float) $matches[1];
                    $lat = (float) $matches[2];
                }
            }
        }

        return [
            'lat' => (float) $lat,
            'lng' => (float) $lng,
        ];
    }

    /**
     * Format timestamp consistently
     *
     * @param mixed $timestamp
     * @return string|null
     */
    protected function formatTimestamp($timestamp): ?string
    {
        if (!$timestamp) {
            return null;
        }

        return Carbon::parse($timestamp)->toIso8601String();
    }
}