<?php

namespace App\Providers\Filament;

use App\Filament\RestaurantSubAdmin\Widgets\RestaurantBookingPanelChart;
use App\Filament\Widgets\RestaurantOverview;
use App\Filament\Widgets\RestaurantPanelChart;
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

class RestaurantSubAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('restaurantSubAdmin')
            ->path('restaurantSubAdmin')
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
                $restaurant = \App\Models\Restaurant::where('admin_id', auth()->id())->first();
                return $restaurant?->name ?? 'Restaurant';
            })
            ->discoverResources(in: app_path('Filament/RestaurantSubAdmin/Resources'), for: 'App\\Filament\\RestaurantSubAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/RestaurantSubAdmin/Pages'), for: 'App\\Filament\\RestaurantSubAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\RestaurantSubAdmin\Pages\RestaurantDiscountEdit::class,
            ])
            ->discoverWidgets(in: app_path('Filament/RestaurantSubAdmin/Widgets'), for: 'App\\Filament\\RestaurantSubAdmin\\Widgets')
            ->widgets([
                RestaurantPanelChart::class,
                RestaurantBookingPanelChart::class,
                RestaurantOverview::class,
                
                // \App\Filament\RestaurantSubAdmin\Pages\RestaurantDiscountEdit::class,
                
            ])
            ->resources([
                \App\Filament\RestaurantSubAdmin\Resources\MenuCategoryResource::class,
                \App\Filament\RestaurantSubAdmin\Resources\MenuItemResource::class,
                \App\Filament\RestaurantAdmin\Resources\RestaurantBookingResource::class,
                \App\Filament\RestaurantSubAdmin\Resources\RestaurantImageResource::class,
                \App\Filament\RestaurantSubAdmin\Resources\RestaurantTableResource::class,
                \App\Filament\Resources\AdminResource::class,
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
