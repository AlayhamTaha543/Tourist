<?php

namespace App\Filament\HotelAdmin\Resources\HotelAmenityResource\Pages;

use App\Filament\HotelAdmin\Resources\HotelAmenityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotelAmenity extends EditRecord
{
    protected static string $resource = HotelAmenityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
