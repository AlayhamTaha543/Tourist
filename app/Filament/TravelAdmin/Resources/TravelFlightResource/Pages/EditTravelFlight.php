<?php

namespace App\Filament\TravelAdmin\Resources\TravelFlightResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelFlightResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTravelFlight extends EditRecord
{
    protected static string $resource = TravelFlightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
