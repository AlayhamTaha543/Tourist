<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\LocationResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLocation extends EditRecord
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}