<?php

namespace App\Filament\TaxiAdmin\Resources\VehicleTypeResource\Pages;

use App\Filament\TaxiAdmin\Resources\VehicleTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleTypes extends ListRecords
{
    protected static string $resource = VehicleTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
