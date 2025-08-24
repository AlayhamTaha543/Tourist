<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\HotelResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\HotelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotel extends EditRecord
{
    protected static string $resource = HotelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
