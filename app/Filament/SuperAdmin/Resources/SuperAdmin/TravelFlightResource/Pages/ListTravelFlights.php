<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\TravelFlightResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\TravelFlightResource;
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
