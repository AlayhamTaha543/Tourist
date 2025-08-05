<?php

namespace App\Filament\TourSubAdmin\Resources\TourScheduleResource\Pages;

use App\Filament\TourSubAdmin\Resources\TourScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourSchedule extends EditRecord
{
    protected static string $resource = TourScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
