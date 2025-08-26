<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\TaxiBookingResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\TaxiBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaxiBooking extends EditRecord
{
    protected static string $resource = TaxiBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
