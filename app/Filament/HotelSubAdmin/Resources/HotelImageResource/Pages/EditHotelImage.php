<?php

namespace App\Filament\HotelSubAdmin\Resources\HotelImageResource\Pages;

use App\Filament\HotelSubAdmin\Resources\HotelImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotelImage extends EditRecord
{
    protected static string $resource = HotelImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
