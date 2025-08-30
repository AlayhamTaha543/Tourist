<?php

namespace App\Filament\TravelAdmin\Resources\FlightTypeResource\Pages;

use App\Filament\TravelAdmin\Resources\FlightTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlightTypes extends ListRecords
{
    protected static string $resource = FlightTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
