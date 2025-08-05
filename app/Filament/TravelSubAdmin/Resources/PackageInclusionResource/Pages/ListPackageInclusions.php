<?php

namespace App\Filament\TravelSubAdmin\Resources\PackageInclusionResource\Pages;

use App\Filament\TravelSubAdmin\Resources\PackageInclusionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackageInclusions extends ListRecords
{
    protected static string $resource = PackageInclusionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
