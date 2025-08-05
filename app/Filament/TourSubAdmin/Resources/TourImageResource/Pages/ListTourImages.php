<?php

namespace App\Filament\TourSubAdmin\Resources\TourImageResource\Pages;

use App\Filament\TourSubAdmin\Resources\TourImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTourImages extends ListRecords
{
    protected static string $resource = TourImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
