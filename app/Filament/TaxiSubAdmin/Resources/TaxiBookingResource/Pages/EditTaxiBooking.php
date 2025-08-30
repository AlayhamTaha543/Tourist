<?php

namespace App\Filament\TaxiSubAdmin\Resources\TaxiBookingResource\Pages;

use App\Filament\TaxiSubAdmin\Resources\TaxiBookingResource;
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
