<?php

namespace App\Filament\TourSubAdmin\Resources\TourTranslationResource\Pages;

use App\Filament\TourSubAdmin\Resources\TourTranslationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourTranslation extends EditRecord
{
    protected static string $resource = TourTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
