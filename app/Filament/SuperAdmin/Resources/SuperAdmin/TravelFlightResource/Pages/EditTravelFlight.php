<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\TravelFlightResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\TravelFlightResource;
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
