<?php

namespace App\Filament\TourAdmin\Resources\TourGuideSkillResource\Pages;

use App\Filament\TourAdmin\Resources\TourGuideSkillResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourGuideSkill extends EditRecord
{
    protected static string $resource = TourGuideSkillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
