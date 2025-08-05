<?php

namespace App\Providers\Filament;

use App\Filament\HotelSubAdmin\Widgets\HotelBookingPanelChart;
use App\Filament\Widgets\HotelOverview;
use App\Filament\Widgets\HotelPanelChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class HotelSubAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hotelSubAdmin')
            ->path('hotelSubAdmin')
            ->authGuard('admin')
            ->login(false)
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Roboto Mono')
            ->brandName('PILOT')
            ->brandName(function () {
                $hotel = \App\Models\Hotel::where('admin_id', auth()->id())->first();
                return $hotel?->name ?? 'hotel';
            })
            ->discoverResources(in: app_path('Filament/HotelSubAdmin/Resources'), for: 'App\\Filament\\HotelSubAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/HotelSubAdmin/Pages'), for: 'App\\Filament\\HotelSubAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->resources([
                \App\Filament\HotelSubAdmin\Resources\RoomTypeResource::class,
                \App\Filament\HotelSubAdmin\Resources\HotelImageResource::class,
                \App\Filament\HotelAdmin\Resources\HotelBookingResource::class,
                \App\Filament\HotelSubAdmin\Resources\HotelAmenityMapResource::class,
                \App\Filament\Resources\AdminResource::class,
            ])
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                HotelBookingPanelChart::class,
                HotelPanelChart::class,
                HotelOverview::class
            ])
            // ->discoverWidgets(in: app_path('Filament/HotelSubAdmin/Widgets'), for: 'App\\Filament\\HotelSubAdmin\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
