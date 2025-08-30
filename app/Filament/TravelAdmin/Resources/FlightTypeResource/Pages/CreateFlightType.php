<?php

namespace App\Filament\TravelAdmin\Resources\TravelAdmin\FlightTypeResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelAdmin\FlightTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFlightType extends CreateRecord
{
    protected static string $resource = FlightTypeResource::class;
}
