<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\RatingResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\RatingResource;
use Filament\Resources\Pages\Page;

class EditRating extends Page
{
    protected static string $resource = RatingResource::class;

    protected static string $view = 'filament.super-admin.resources.super-admin.rating-resource.pages.edit-rating';
}
