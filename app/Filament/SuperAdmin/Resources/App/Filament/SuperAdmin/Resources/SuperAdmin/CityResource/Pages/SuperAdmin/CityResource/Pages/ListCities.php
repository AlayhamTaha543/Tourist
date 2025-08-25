<?php

namespace App\Filament\SuperAdmin\Resources\App\Filament\SuperAdmin\Resources\SuperAdmin\CityResource\Pages\SuperAdmin\CityResource\Pages;

use App\Filament\SuperAdmin\Resources\App\Filament\SuperAdmin\Resources\SuperAdmin\CityResource;
use Filament\Resources\Pages\Page;

class ListCities extends Page
{
    protected static string $resource = CityResource::class;

    protected static string $view = 'filament.super-admin.resources.app.filament.super-admin.resources.super-admin.city-resource.pages.super-admin.city-resource.pages.list-cities';
}
