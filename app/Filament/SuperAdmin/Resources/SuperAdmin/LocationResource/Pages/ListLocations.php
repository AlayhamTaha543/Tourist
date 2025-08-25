<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\LocationResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\SuperAdmin\Widgets\LocationMapWidget::class,
        ];
    }
}
