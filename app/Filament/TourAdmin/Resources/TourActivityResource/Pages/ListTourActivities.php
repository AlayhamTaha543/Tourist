<?php

namespace App\Filament\TourAdmin\Resources\TourActivityResource\Pages;

use App\Filament\TourAdmin\Resources\TourActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTourActivities extends ListRecords
{
    protected static string $resource = TourActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
