<?php

namespace App\Filament\TravelSubAdmin\Resources\TravelFlightResource\Pages;

use App\Filament\TravelSubAdmin\Resources\TravelFlightResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTravelFlight extends EditRecord
{
    protected static string $resource = TravelFlightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
