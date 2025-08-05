<?php

namespace App\Filament\TravelSubAdmin\Resources\TravelFlightResource\Pages;

use App\Filament\TravelSubAdmin\Resources\TravelFlightResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTravelFlight extends ViewRecord
{
    protected static string $resource = TravelFlightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
