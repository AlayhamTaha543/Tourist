<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\TripResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\TripResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrips extends ListRecords
{
    protected static string $resource = TripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
