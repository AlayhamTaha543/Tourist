<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
