<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\UserResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
