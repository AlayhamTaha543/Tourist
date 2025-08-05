<?php

namespace App\Providers\Filament;

use App\Filament\TourAdmin\Pages\NotificationsPage;
use App\Filament\TourAdmin\Pages\TourAdminNotifications;
use App\Filament\TourAdmin\Widgets\TourBookingPanelChart;
use App\Filament\TourAdmin\Widgets\TourPanelChart;
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

class TourAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tourAdmin')
            ->path('tourAdmin')
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
            ->discoverResources(in: app_path('Filament/TourAdmin/Resources'), for: 'App\\Filament\\TourAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/TourAdmin/Pages'), for: 'App\\Filament\\TourAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
                
            NotificationsPage::class
            ])

            ->resources([
                \App\Filament\TourAdmin\Resources\TourResource::class,
                \App\Filament\TourAdmin\Resources\TourBookingResource::class,
                \App\Filament\TourAdmin\Resources\TourCategoryResource::class,
                \App\Filament\Resources\AdminResource::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/TourAdmin/Widgets'), for: 'App\\Filament\\TourAdmin\\Widgets')
            ->widgets([
                TourBookingPanelChart::class,
                TourPanelChart::class,

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
