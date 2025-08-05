<?php

namespace App\Filament\TaxiAdmin\Resources\VehicleTypeResource\Pages;

use App\Filament\TaxiAdmin\Resources\VehicleTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleType extends EditRecord
{
    protected static string $resource = VehicleTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
