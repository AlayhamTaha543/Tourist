<?php

namespace App\Filament\TaxiAdmin\Resources\RentalVehicleResource\Pages;

use App\Filament\TaxiAdmin\Resources\RentalVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class TaxiDashboard extends ListRecords
{
    protected static string $resource = RentalVehicleResource::class;
}
