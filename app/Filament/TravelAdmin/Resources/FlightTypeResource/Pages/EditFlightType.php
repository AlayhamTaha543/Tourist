<?php

namespace App\Filament\TravelAdmin\Resources\TravelAdmin\FlightTypeResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelAdmin\FlightTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlightType extends EditRecord
{
    protected static string $resource = FlightTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
