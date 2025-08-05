<?php

namespace App\Filament\TourSubAdmin\Resources\TourTranslationResource\Pages;

use App\Filament\TourSubAdmin\Resources\TourTranslationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTourTranslation extends ViewRecord
{
    protected static string $resource = TourTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
