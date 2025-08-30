<?php

namespace App\Filament\TourAdmin\Resources\TourImageResource\Pages;

use App\Filament\TourAdmin\Resources\TourImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourImage extends EditRecord
{
    protected static string $resource = TourImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
