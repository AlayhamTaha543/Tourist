<?php

namespace App\Filament\TravelAdmin\Resources\TravelBookingResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTravelBooking extends EditRecord
{
    protected static string $resource = TravelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
