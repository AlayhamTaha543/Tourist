<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\TripResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\TripResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrip extends EditRecord
{
    protected static string $resource = TripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
