<?php

namespace App\Filament\TravelSubAdmin\Resources\TravelBookingResource\Pages;

use App\Filament\TravelSubAdmin\Resources\TravelBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTravelBooking extends EditRecord
{
    protected static string $resource = TravelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
