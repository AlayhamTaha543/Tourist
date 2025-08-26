<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleTypeResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleTypeResource;
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
