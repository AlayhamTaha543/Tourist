<?php

namespace App\Filament\RentAdmin\Resources\RentalOfficeResource\Pages;

use App\Filament\RentAdmin\Resources\RentalOfficeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentalOffice extends EditRecord
{
    protected static string $resource = RentalOfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
