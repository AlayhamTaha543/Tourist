<?php

namespace App\Filament\TourSubAdmin\Resources\PackageBookingResource\Pages;

use App\Filament\TourSubAdmin\Resources\PackageBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackageBookings extends ListRecords
{
    protected static string $resource = PackageBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
