<?php

namespace App\Filament\TourAdmin\Resources\TourBookingResource\Pages;

use App\Filament\TourAdmin\Resources\TourBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTourBookings extends ListRecords
{
    protected static string $resource = TourBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
