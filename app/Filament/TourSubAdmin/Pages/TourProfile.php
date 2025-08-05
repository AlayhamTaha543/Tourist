<?php

namespace App\Filament\TourSubAdmin\Pages;

use App\Models\Tour;
use Filament\Pages\Page;

class TourProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.tour-sub-admin.pages.tour-profile';
    public $tour;
    public function mount(): void
    {
        $this->tour = Tour::with(['admin', 'schedules'])
        ->where('admin_id', auth()->id())
        ->firstOrFail();
    }
}
