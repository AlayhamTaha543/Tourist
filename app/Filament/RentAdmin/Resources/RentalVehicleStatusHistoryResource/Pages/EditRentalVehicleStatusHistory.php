<?php

namespace App\Filament\RentAdmin\Resources\RentalVehicleStatusHistoryResource\Pages;

use App\Filament\RentAdmin\Resources\RentalVehicleStatusHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentalVehicleStatusHistory extends EditRecord
{
    protected static string $resource = RentalVehicleStatusHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
