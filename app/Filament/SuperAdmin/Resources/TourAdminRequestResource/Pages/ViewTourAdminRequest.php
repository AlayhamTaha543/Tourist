<?php

namespace App\Filament\SuperAdmin\Resources\TourAdminRequestResource\Pages;

use App\Filament\SuperAdmin\Resources\TourAdminRequestResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewTourAdminRequest extends ViewRecord // Changed to ViewRecord
{
    protected static string $resource = TourAdminRequestResource::class;

    protected static string $view = 'filament.super-admin.resources.tour-admin-request-resource.pages.view-tour-admin-request';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->readOnly(),
                Forms\Components\TextInput::make('email')
                    ->readOnly(),
                Forms\Components\TextInput::make('age')
                    ->readOnly(),
                Forms\Components\TagsInput::make('skills')
                    ->readOnly(),
                Forms\Components\FileUpload::make('personal_image')
                    ->image()
                    ->disk('public')
                    ->directory('tour_admin_requests/personal_images')
                    ->readOnly(),
                Forms\Components\FileUpload::make('certificate_image')
                    ->image()
                    ->disk('public')
                    ->directory('tour_admin_requests/certificate_images')
                    ->readOnly(),
                Forms\Components\TextInput::make('status')
                    ->readOnly(),
            ]);
    }
}