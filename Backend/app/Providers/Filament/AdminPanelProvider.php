<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\CatalogShortcutsWidget;
use App\Filament\Widgets\LatestReportsWidget;
use App\Filament\Widgets\RegionalAlertMapWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Http\Middleware\SetFilamentLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Green,
            ])
            ->brandName(fn (): string => __('filament.brand.name'))
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn (): string => view('filament.locale-switcher')->render(),
            )
            ->renderHook(
                PanelsRenderHook::SCRIPTS_AFTER,
                fn (): string => view('filament.hooks.fcm-web-push')->render(),
            )
            ->navigationGroups([
                NavigationGroup::make(fn (): string => __('filament.nav_dashboard')),
                NavigationGroup::make(fn (): string => __('filament.nav_locations')),
                NavigationGroup::make(fn (): string => __('filament.nav_animals')),
                NavigationGroup::make(fn (): string => __('filament.nav_reports')),
                NavigationGroup::make(fn (): string => __('filament.nav_users')),
                NavigationGroup::make(fn (): string => __('filament.nav_notifications')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                StatsOverviewWidget::class,
                CatalogShortcutsWidget::class,
                LatestReportsWidget::class,
                RegionalAlertMapWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                SetFilamentLocale::class,
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
