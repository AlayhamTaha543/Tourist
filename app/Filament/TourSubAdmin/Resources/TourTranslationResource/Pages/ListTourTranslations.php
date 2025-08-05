<?php

namespace App\Filament\TourSubAdmin\Resources\TourTranslationResource\Pages;

use App\Filament\TourSubAdmin\Resources\TourTranslationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTourTranslations extends ListRecords
{
    protected static string $resource = TourTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
