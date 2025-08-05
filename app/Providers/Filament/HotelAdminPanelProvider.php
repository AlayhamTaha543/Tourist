<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AdminPanelOverview;
use App\Filament\Widgets\HotelPanelChart;
use App\Filament\Widgets\UserPanelChart;
use App\Http\Middleware\AuthMiddleware;
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

class HotelAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hotelAdmin')
            ->path('hotelAdmin')
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
            ->resources([
                \App\Filament\HotelAdmin\Resources\HotelAmenityResource::class,
                \App\Filament\HotelAdmin\Resources\HotelBookingResource::class,
                \App\Filament\HotelAdmin\Resources\HotelResource::class,
                \App\Filament\Resources\AdminResource::class,
            ])
            ->discoverResources(in: app_path('Filament/HotelAdmin/Resources'), for: 'App\\Filament\\HotelAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/HotelAdmin/Pages'), for: 'App\\Filament\\HotelAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/HotelAdmin/Widgets'), for: 'App\\Filament\\HotelAdmin\\Widgets')
            ->widgets([
                HotelPanelChart::class,
                AdminPanelOverview::class,
                UserPanelChart::class
            ])
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
                // AuthMiddleware::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
