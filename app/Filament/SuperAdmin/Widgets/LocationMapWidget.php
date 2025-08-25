<?php

namespace App\Filament\SuperAdmin\Widgets;

use Webbingbrasil\FilamentMaps\Actions;
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;
use App\Models\Location; // Import your Location model
use Illuminate\Support\Collection; // For type hinting
use Illuminate\Support\Facades\Config; // To access Geoapify key

class LocationMapWidget extends MapWidget
{
    // Set the widget to span the full width of its container
    protected int | string | array $columnSpan = 'full';

    // Remove the border around the map for a cleaner look
    protected bool $hasBorder = false;

    // Set the height of the map
    protected ?string $height = '500px';

    // Configure the map to be rounded
    protected bool $rounded = true;

    // Define the default tile layer URL (OpenStreetMap)
    protected string $tileLayerUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

    // Define tile layer options, including attribution
    protected array $tileLayerOptions = [
        'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    ];

    // Override setUp to use Geoapify if configured
    public function setUp(): void
    {
        parent::setUp();

        $apiKey = Config::get('services.geoapify.key');
        if ($apiKey) {
            $this->tileLayerUrl("https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey={$apiKey}");
            $this->tileLayerOptions([
                'attribution' => 'Powered by <a href="https://www.geoapify.com/" target="_blank">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',
            ]);
        }
    }

    /**
     * Dynamically retrieve markers from the database.
     *
     * @return array<Marker>
     */
    public function getMarkers(): array
    {
        // Fetch all locations from the database
        $locations = Location::all();

        // Map each location to a FilamentMaps Marker object
        return $locations->map(function (Location $location) {
            return Marker::make('location_' . $location->id) // Unique ID for each marker
                ->lat($location->latitude)
                ->lng($location->longitude)
                ->popup($location->name) // Show location name in popup
                ->tooltip('Click for details'); // Tooltip on hover
        })->toArray();
    }

    /**
     * Define actions available on the map widget.
     *
     * @return array<Actions\Action>
     */
    public function getActions(): array
    {
        return [
            // Zoom in/out action
            Actions\ZoomAction::make(),

            // Center map on Amsterdam with a specific zoom level
            Actions\CenterMapAction::make()
                ->centerTo([52.3676, 4.9041]) // Amsterdam coordinates
                ->zoom(10),
        ];
    }
}
