<?php

namespace App\Filament\RentAdmin\Resources\RentalBookingResource\Pages;

use App\Filament\RentAdmin\Resources\RentalBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentalBooking extends EditRecord
{
    protected static string $resource = RentalBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
