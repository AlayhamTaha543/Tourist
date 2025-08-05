<?php

namespace App\Filament\RentAdmin\Resources\RentalVehicleResource\Pages;

use App\Filament\RentAdmin\Resources\RentalVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentalVehicle extends EditRecord
{
    protected static string $resource = RentalVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
