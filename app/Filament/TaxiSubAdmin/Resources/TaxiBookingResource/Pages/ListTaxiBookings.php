<?php

namespace App\Filament\TaxiSubAdmin\Resources\TaxiBookingResource\Pages;

use App\Filament\TaxiSubAdmin\Resources\TaxiBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaxiBookings extends ListRecords
{
    protected static string $resource = TaxiBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
