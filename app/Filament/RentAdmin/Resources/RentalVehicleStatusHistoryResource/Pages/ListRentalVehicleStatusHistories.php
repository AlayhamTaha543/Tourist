<?php

namespace App\Filament\RentAdmin\Resources\RentalVehicleStatusHistoryResource\Pages;

use App\Filament\RentAdmin\Resources\RentalVehicleStatusHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalVehicleStatusHistories extends ListRecords
{
    protected static string $resource = RentalVehicleStatusHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
