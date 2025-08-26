<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\DriverVehicleAssignmentResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\DriverVehicleAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDriverVehicleAssignment extends EditRecord
{
    protected static string $resource = DriverVehicleAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
