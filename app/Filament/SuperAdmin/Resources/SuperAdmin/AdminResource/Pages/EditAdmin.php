<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\AdminResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
