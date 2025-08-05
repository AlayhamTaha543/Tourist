<?php

namespace App\Filament\RentAdmin\Resources\RentalBookingResource\Pages;

use App\Filament\RentAdmin\Resources\RentalBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalBookings extends ListRecords
{
    protected static string $resource = RentalBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
