<?php

namespace App\Filament\TourAdmin\Resources\TourCategoryResource\Pages;

use App\Filament\TourAdmin\Resources\TourCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTourCategory extends ViewRecord
{
    protected static string $resource = TourCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
