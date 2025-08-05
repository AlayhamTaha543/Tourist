<?php

namespace App\Filament\HotelAdmin\Resources\HotelResource\Pages;

use App\Filament\HotelAdmin\Resources\HotelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHotel extends ViewRecord
{
    protected static string $resource = HotelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
