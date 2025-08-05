<?php

namespace App\Filament\TourSubAdmin\Resources\TourScheduleResource\Pages;

use App\Filament\TourSubAdmin\Resources\TourScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTourSchedule extends ViewRecord
{
    protected static string $resource = TourScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
