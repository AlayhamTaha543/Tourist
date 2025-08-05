<?php

namespace App\Providers\Filament;

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

class TravelSubAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('travelSubAdmin')
            ->path('travelSubAdmin')
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
                $travel = \App\Models\TravelAgency::where('admin_id', auth()->id())->first();
                return $travel?->name ?? 'travel';
            })
            ->discoverResources(in: app_path('Filament/TravelSubAdmin/Resources'), for: 'App\\Filament\\TravelSubAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/TravelSubAdmin/Pages'), for: 'App\\Filament\\TravelSubAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->resources([
                \App\Filament\TravelSubAdmin\Resources\TravelFlightResource::class,
                \App\Filament\TravelSubAdmin\Resources\TravelPackageResource::class,
                \App\Filament\TravelSubAdmin\Resources\PackageDestinationResource::class,
                \App\Filament\TravelSubAdmin\Resources\PackageInclusionResource::class,
                \App\Filament\TravelAdmin\Resources\TravelBookingResource::class,
                \App\Filament\Resources\AdminResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/TravelSubAdmin/Widgets'), for: 'App\\Filament\\TravelSubAdmin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
