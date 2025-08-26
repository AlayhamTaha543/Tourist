<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\DriverVehicleAssignmentResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\DriverVehicleAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDriverVehicleAssignments extends ListRecords
{
    protected static string $resource = DriverVehicleAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
