<?php

namespace App\Providers\Filament;

use App\Models\Tour;
use Filament\Facades\Filament;
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

class TourSubAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tourSubAdmin')
            ->path('tourSubAdmin')
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
                $tour = Tour::where('admin_id', auth()->id())->first();
                return $tour?->name ?? 'tour';
            })
            ->discoverResources(in: app_path('Filament/TourSubAdmin/Resources'), for: 'App\\Filament\\TourSubAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/TourSubAdmin/Pages'), for: 'App\\Filament\\TourSubAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/TourSubAdmin/Widgets'), for: 'App\\Filament\\TourSubAdmin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->resources([
                \App\Filament\TourAdmin\Resources\TourBookingResource::class,
                \App\Filament\TourSubAdmin\Resources\TourImageResource::class,
                \App\Filament\TourSubAdmin\Resources\TourScheduleResource::class,
                \App\Filament\TourSubAdmin\Resources\TourTranslationResource::class,
                \App\Filament\TourSubAdmin\Resources\PackageBookingResource::class,
                \App\Filament\TourAdmin\Resources\TourCategoryResource::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
