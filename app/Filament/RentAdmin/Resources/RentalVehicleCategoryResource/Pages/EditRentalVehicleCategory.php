<?php

namespace App\Filament\RentAdmin\Resources\RentalVehicleCategoryResource\Pages;

use App\Filament\RentAdmin\Resources\RentalVehicleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentalVehicleCategory extends EditRecord
{
    protected static string $resource = RentalVehicleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
