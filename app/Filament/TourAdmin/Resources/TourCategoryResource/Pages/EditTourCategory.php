<?php

namespace App\Filament\TourAdmin\Resources\TourCategoryResource\Pages;

use App\Filament\TourAdmin\Resources\TourCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourCategory extends EditRecord
{
    protected static string $resource = TourCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
