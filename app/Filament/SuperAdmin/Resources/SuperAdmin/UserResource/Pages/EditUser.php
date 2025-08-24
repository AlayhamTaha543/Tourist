<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\UserResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
