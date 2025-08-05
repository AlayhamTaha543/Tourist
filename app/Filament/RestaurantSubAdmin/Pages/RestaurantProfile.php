<?php

namespace App\Filament\RestaurantSubAdmin\Pages;

use App\Models\Restaurant;
use Filament\Pages\Page;

class RestaurantProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.restaurant-sub-admin.pages.restaurant-profile';

    public $restaurant;
    // public function shouldRegisterNavigation(): bool
    // {
    //     return auth()->check(); 
    // }

    public function mount(): void
    {
        $this->restaurant = Restaurant::where('admin_id', auth()->id())->firstOrFail();
    }
}
