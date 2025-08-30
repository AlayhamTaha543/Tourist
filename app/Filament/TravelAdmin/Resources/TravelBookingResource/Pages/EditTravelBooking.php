<?php

namespace App\Filament\TravelAdmin\Resources\TravelAdmin\TravelBookingResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelAdmin\TravelBookingResource;
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
