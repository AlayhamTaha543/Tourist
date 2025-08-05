<?php

namespace App\Filament\TourAdmin\Resources\TourBookingResource\Pages;

use App\Filament\TourAdmin\Resources\TourBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourBooking extends EditRecord
{
    protected static string $resource = TourBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
