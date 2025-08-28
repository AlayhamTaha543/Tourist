<?php

namespace App\Filament\SuperAdmin\Resources\TourAdminRequestResource\Pages;

use App\Filament\SuperAdmin\Resources\TourAdminRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTourAdminRequests extends ListRecords
{
    protected static string $resource = TourAdminRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
