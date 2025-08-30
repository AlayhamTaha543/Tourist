<?php

namespace App\Filament\TravelAdmin\Resources\TravelAdmin\TravelFlightResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelAdmin\TravelFlightResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTravelFlights extends ListRecords
{
    protected static string $resource = TravelFlightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
