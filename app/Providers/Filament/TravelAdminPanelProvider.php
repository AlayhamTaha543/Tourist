<?php

namespace App\Providers\Filament;

use App\Filament\TravelAdmin\Widgets\TravelBookingPanelChart;
use App\Filament\Widgets\TravelOverview;
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

class TravelAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('travelAdmin')
            ->path('travelAdmin')
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
            ->discoverResources(in: app_path('Filament/TravelAdmin/Resources'), for: 'App\\Filament\\TravelAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/TravelAdmin/Pages'), for: 'App\\Filament\\TravelAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->resources([
                \App\Filament\TravelAdmin\Resources\TravelAgencyResource::class,
                \App\Filament\TravelAdmin\Resources\TravelBookingResource::class,
                \App\Filament\Resources\AdminResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/TravelAdmin/Widgets'), for: 'App\\Filament\\TravelAdmin\\Widgets')
            ->widgets([
                TravelBookingPanelChart::class,
                TravelOverview::class
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
