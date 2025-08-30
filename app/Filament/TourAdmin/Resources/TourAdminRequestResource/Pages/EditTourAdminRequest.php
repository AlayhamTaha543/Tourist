<?php

namespace App\Filament\TourAdmin\Resources\TourAdminRequestResource\Pages;

use App\Filament\TourAdmin\Resources\TourAdminRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourAdminRequest extends EditRecord
{
    protected static string $resource = TourAdminRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
