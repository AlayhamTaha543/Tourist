<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\TourResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\TourResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTours extends ListRecords
{
    protected static string $resource = TourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
