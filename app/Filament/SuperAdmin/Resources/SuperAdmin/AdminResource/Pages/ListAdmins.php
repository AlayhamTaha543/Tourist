<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\AdminResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdmins extends ListRecords
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
