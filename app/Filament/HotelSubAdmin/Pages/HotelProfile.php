<?php

namespace App\Filament\HotelSubAdmin\Pages;

use App\Models\Hotel;
use Filament\Pages\Page;

class HotelProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.hotel-sub-admin.pages.hotel-profile';
    public $hotel;
    public function mount(): void
    {
        $this->hotel = Hotel::where('admin_id', auth()->id())->firstOrFail();
    }
}
