<?php

namespace App\Filament\RentAdmin\Resources\RentalVehicleResource\Pages;

use App\Filament\RentAdmin\Resources\RentalVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalVehicles extends ListRecords
{
    protected static string $resource = RentalVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
