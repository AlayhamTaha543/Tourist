<?php

namespace App\Filament\TravelSubAdmin\Pages;

use App\Models\TravelAgency;
use App\Models\TravelPackage;
use Filament\Pages\Page;

class TravelProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.travel-sub-admin.pages.travel-profile';
    public $package;

    public function mount(): void
    {   $agency=TravelAgency::where('admin_id', auth()->id())->first();
        $this->package = TravelPackage::with(['agency', 'destinations', 'inclusions']) 
            ->where('agency_id',$agency->id )
            ->firstOrFail();
    }
}
