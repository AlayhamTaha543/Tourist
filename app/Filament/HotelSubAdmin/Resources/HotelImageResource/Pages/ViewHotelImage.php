<?php

namespace App\Filament\HotelSubAdmin\Resources\HotelImageResource\Pages;

use App\Filament\HotelSubAdmin\Resources\HotelImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHotelImage extends ViewRecord
{
    protected static string $resource = HotelImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
