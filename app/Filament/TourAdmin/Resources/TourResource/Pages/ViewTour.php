<?php

namespace App\Filament\TourAdmin\Resources\TourResource\Pages;

use App\Filament\TourAdmin\Resources\TourResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTour extends ViewRecord
{
    protected static string $resource = TourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
