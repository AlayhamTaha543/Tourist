<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
