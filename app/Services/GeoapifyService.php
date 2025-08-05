<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeoapifyService
{
    protected $mapTilesKey;
    protected $geocodingKey;
    protected $routingKey;
    protected $placesKey;

    public function __construct()
    {
        $this->mapTilesKey = env('GEOAPIFY_MAP_TILES_KEY');
        $this->geocodingKey = env('GEOAPIFY_GEOCODING_KEY');
        $this->routingKey = env('GEOAPIFY_ROUTING_KEY');
        $this->placesKey = env('GEOAPIFY_PLACES_KEY');
    }

    /**
     * Get map tiles URL
     *
     * @param string $style Map style (osm-bright, osm-carto, etc.)
     * @return string
     */
    public function getMapTilesUrl($style = 'osm-bright')
    {
        return "https://maps.geoapify.com/v1/tile/{$style}/{z}/{x}/{y}.png?apiKey={$this->mapTilesKey}";
    }

    /**
     * Geocode an address to coordinates
     *
     * @param string $address Address to geocode
     * @return array|null
     */
    public function geocodeAddress($address)
    {
        $response = Http::get('https://api.geoapify.com/v1/geocode/search', [
            'text' => $address,
            'apiKey' => $this->geocodingKey,
            'format' => 'json'
        ]);

        if ($response->successful() && isset($response->json()['features'][0])) {
            $feature = $response->json()['features'][0];
            return [
                'lat' => $feature['properties']['lat'],
                'lon' => $feature['properties']['lon'],
                'formatted' => $feature['properties']['formatted']
            ];
        }

        return null;
    }

    /**
     * Reverse geocode coordinates to address
     *
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return array|null
     */
    public function reverseGeocode($lat, $lon)
    {
        $response = Http::get('https://api.geoapify.com/v1/geocode/reverse', [
            'lat' => $lat,
            'lon' => $lon,
            'apiKey' => $this->geocodingKey,
            'format' => 'json'
        ]);

        if ($response->successful() && isset($response->json()['features'][0])) {
            $feature = $response->json()['features'][0];
            return [
                'formatted' => $feature['properties']['formatted'],
                'country' => $feature['properties']['country'],
                'city' => $feature['properties']['city'],
                'street' => $feature['properties']['street'],
                'housenumber' => $feature['properties']['housenumber'] ?? null,
                'postcode' => $feature['properties']['postcode'] ?? null
            ];
        }

        return null;
    }

    /**
     * Get routing information between two points
     *
     * @param float $fromLat Starting latitude
     * @param float $fromLon Starting longitude
     * @param float $toLat Destination latitude
     * @param float $toLon Destination longitude
     * @param string $mode Transportation mode (drive, walk, bicycle, etc.)
     * @return array|null
     */
    public function getRoute($fromLat, $fromLon, $toLat, $toLon, $mode = 'drive')
    {
        $response = Http::get('https://api.geoapify.com/v1/routing', [
            'waypoints' => "{$fromLat},{$fromLon}|{$toLat},{$toLon}",
            'mode' => $mode,
            'apiKey' => $this->routingKey
        ]);

        if ($response->successful() && isset($response->json()['features'][0])) {
            $feature = $response->json()['features'][0];
            return [
                'distance' => $feature['properties']['distance'],
                'time' => $feature['properties']['time'],
                'route' => $feature['geometry']['coordinates']
            ];
        }

        return null;
    }

    /**
     * Search for places
     *
     * @param string $categories Categories to search for
     * @param float $lat Latitude for center of search
     * @param float $lon Longitude for center of search
     * @param int $radius Search radius in meters
     * @return array|null
     */
    public function searchPlaces($categories, $lat, $lon, $radius = 1000)
    {
        $response = Http::get('https://api.geoapify.com/v2/places', [
            'categories' => $categories,
            'filter' => "circle:{$lon},{$lat},{$radius}",
            'limit' => 20,
            'apiKey' => $this->placesKey
        ]);

        if ($response->successful() && isset($response->json()['features'])) {
            return $response->json()['features'];
        }

        return null;
    }

    /**
     * Get place details
     *
     * @param string $placeId Place ID
     * @return array|null
     */
    public function getPlaceDetails($placeId)
    {
        $response = Http::get("https://api.geoapify.com/v2/place-details", [
            'id' => $placeId,
            'apiKey' => $this->placesKey
        ]);

        if ($response->successful() && isset($response->json()['features'][0])) {
            return $response->json()['features'][0];
        }

        return null;
    }
}
