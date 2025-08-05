<?php

namespace App\Filament\TourSubAdmin\Resources\TourImageResource\Pages;

use App\Filament\TourSubAdmin\Resources\TourImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourImage extends EditRecord
{
    protected static string $resource = TourImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
