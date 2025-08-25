<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Livewire\Component as LivewireComponent; // Alias to avoid conflict

class MapPickerField extends Field
{
    protected string $view = 'forms.components.map-picker-field';

    public ?float $latitude = null;
    public ?float $longitude = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (MapPickerField $component, $state) {
            // When the form loads, set the initial latitude and longitude
            $component->latitude = $component->getStatePath(true) ? $component->getRecord()->latitude : null;
            $component->longitude = $component->getStatePath(true) ? $component->getRecord()->longitude : null;
        });

        $this->dehydrateStateUsing(function (MapPickerField $component) {
            // When the form saves, ensure the state is correctly passed
            return [
                'latitude' => $component->latitude,
                'longitude' => $component->longitude,
            ];
        });
    }

    public function latitude(?float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function longitude(?float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    // Livewire listener to update internal state from JS
    public function updatedLatitude($value)
    {
        $this->latitude = (float) $value;
        $this->getLivewire()->fill([
            $this->getStatePath() . '.latitude' => $this->latitude,
        ]);
    }

    public function updatedLongitude($value)
    {
        $this->longitude = (float) $value;
        $this->getLivewire()->fill([
            $this->getStatePath() . '.longitude' => $this->longitude,
        ]);
    }
}
