<?php

namespace App\Filament\TourSubAdmin\Resources\TourImageResource\Pages;

use App\Filament\TourSubAdmin\Resources\TourImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTourImage extends ViewRecord
{
    protected static string $resource = TourImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
