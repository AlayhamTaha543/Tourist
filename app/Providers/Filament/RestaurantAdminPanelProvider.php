<?php

namespace App\Providers\Filament;

use App\Filament\RestaurantSubAdmin\Widgets\RestaurantBookingPanelChart;
use App\Filament\Widgets\AdminPanelOverview;
use App\Filament\Widgets\RestaurantOverview;
use App\Filament\Widgets\RestaurantPanelChart;
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

class RestaurantAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('restaurantAdmin')
            ->path('restaurantAdmin')
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
            // ->brandLogo(asset('images/logo.svg'))
            // ->favicon(asset('images/profile.jpg'))
            ->discoverResources(in: app_path('Filament/RestaurantAdmin/Resources'), for: 'App\\Filament\\RestaurantAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/RestaurantAdmin/Pages'), for: 'App\\Filament\\RestaurantAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->resources([
                \App\Filament\RestaurantAdmin\Resources\RestaurantResource::class,
                \App\Filament\RestaurantAdmin\Resources\RestaurantBookingResource::class,
                \App\Filament\Resources\AdminResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/RestaurantAdmin/Widgets'), for: 'App\\Filament\\RestaurantAdmin\\Widgets')
            ->widgets([
                RestaurantPanelChart::class,
                RestaurantBookingPanelChart::class,
                RestaurantOverview::class
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
