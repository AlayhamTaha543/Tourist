<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleTypeResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\VehicleTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicleType extends CreateRecord
{
    protected static string $resource = VehicleTypeResource::class;
}
