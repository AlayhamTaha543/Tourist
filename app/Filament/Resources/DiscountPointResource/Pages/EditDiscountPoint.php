<?php

namespace App\Filament\Resources\DiscountPointResource\Pages;

use App\Filament\Resources\DiscountPointResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiscountPoint extends EditRecord
{
    protected static string $resource = DiscountPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
