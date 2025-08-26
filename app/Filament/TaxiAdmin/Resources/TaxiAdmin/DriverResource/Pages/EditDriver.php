<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\DriverResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\DriverResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
