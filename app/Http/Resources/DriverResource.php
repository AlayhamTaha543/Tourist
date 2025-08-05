<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;

class DriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $isAdmin = $request->user()?->hasRole('admin');
        $isDriverOwner = $request->user()?->driver?->id === $this->id;

        $data = [
            'id' => $this->id,
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'license_number' => $this->license_number,
            'experience_years' => $this->experience_years,
            'rating' => (float) $this->rating,
            'rating_count' => $this->rating_count,
            'availability_status' => $this->availability_status,
            'is_active' => (bool) $this->is_active,
            'last_seen_at' => $this->formatTimestamp($this->last_seen_at),
            'location_updated_at' => $this->formatTimestamp($this->location_updated_at),
            'shift_start' => $this->shift_start?->format('H:i'),
            'shift_end' => $this->shift_end?->format('H:i'),
            'taxi_service' => $this->whenLoaded('taxiService', fn() => [
                'id' => $this->taxiService->id,
                'name' => $this->taxiService->name,
            ]),
            'active_vehicle' => $this->whenLoaded(
                'activeVehicle',
                fn() =>
                $this->activeVehicle ? VehicleResource::make($this->activeVehicle) : null
            ),
            'vehicles' => VehicleResource::collection($this->whenLoaded('vehicles')),
            'trips' => TripResource::collection($this->whenLoaded('trips')),
            'created_at' => $this->formatTimestamp($this->created_at),
            'updated_at' => $this->formatTimestamp($this->updated_at),
        ];

        // Conditionally include location data
        // if ($this->shouldIncludeLocation($isAdmin)) {
        //     $data['location'] = $this->getLocationData();
        // }

        // Add HATEOAS links
        $data['_links'] = $this->getLinks($isDriverOwner);

        // Add pagination metadata if needed
        if ($this->resource instanceof \Illuminate\Pagination\AbstractPaginator) {
            $data['_links']['pagination'] = $this->getPaginationData();
        }

        return $data;
    }

    protected function shouldIncludeLocation(bool $isAdmin): bool
    {
        return $this->availability_status === 'available' || $isAdmin;
    }

    protected function getLocationData(): array
    {
        return [
            'coordinates' => $this->getCoordinates(),
            'updated_at' => $this->formatTimestamp($this->location_updated_at),
        ];
    }

    protected function getCoordinates(): array
    {
        try {
            return [
                'lat' => $this->current_location->getLat(),
                'lng' => $this->current_location->getLng(),
            ];
        } catch (\Exception $e) {
            return $this->fallbackCoordinateExtraction();
        }
    }

    protected function fallbackCoordinateExtraction(): array
    {
        // Backup method if package fails
        if (is_string($this->current_location)) {
            preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $this->current_location, $matches);
            return count($matches) === 3 ? [
                'lng' => (float) $matches[1],
                'lat' => (float) $matches[2],
            ] : ['lat' => 0, 'lng' => 0];
        }

        return ['lat' => 0, 'lng' => 0];
    }

    protected function getLinks(bool $isDriverOwner): array
    {
        $links = [
            'self' => [
                'href' => route('api.drivers.show', $this->id),
                'method' => 'GET',
            ],
        ];

        if ($isDriverOwner) {
            $links += [
                'update_location' => [
                    'href' => route('api.drivers.updateLocation'),
                    'method' => 'POST',
                ],
                'update_status' => [
                    'href' => route('api.drivers.updateStatus'),
                    'method' => 'POST',
                ],
                'trips' => [
                    'href' => route('api.drivers.trips'),
                    'method' => 'GET',
                ],
            ];
        }

        return $links;
    }

    protected function getPaginationData(): array
    {
        return [
            'total' => $this->resource->total(),
            'per_page' => $this->resource->perPage(),
            'current_page' => $this->resource->currentPage(),
            'last_page' => $this->resource->lastPage(),
        ];
    }

    protected function formatTimestamp($timestamp): ?string
    {
        return $timestamp?->toIso8601String();
    }
}
