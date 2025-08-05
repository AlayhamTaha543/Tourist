<?php

namespace App\Filament\TourSubAdmin\Resources\PackageBookingResource\Pages;

use App\Filament\TourSubAdmin\Resources\PackageBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackageBooking extends EditRecord
{
    protected static string $resource = PackageBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
