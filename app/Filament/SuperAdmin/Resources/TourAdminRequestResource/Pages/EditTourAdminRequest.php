<?php

namespace App\Filament\SuperAdmin\Resources\TourAdminRequestResource\Pages;

use App\Filament\SuperAdmin\Resources\TourAdminRequestResource;
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
