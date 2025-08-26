<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\TripResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\TripResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTrip extends CreateRecord
{
    protected static string $resource = TripResource::class;
}
