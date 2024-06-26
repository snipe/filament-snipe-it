<?php

namespace App\Providers\Filament;


use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
//use App\Filament\Pages\Auth\EditProfile;
use Filament\Navigation\MenuItem;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Filament\Navigation\NavigationGroup;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use App\Http\Middleware\CheckForDebug;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use \Hasnayeen\Themes\Http\Middleware\SetTheme;
use \Hasnayeen\Themes\ThemesPlugin;


class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->sidebarWidth('15rem')
            ->sidebarCollapsibleOnDesktop()
            ->breadcrumbs(true)
            ->id('admin')
            ->path('')
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
               // Widgets\AccountWidget::class,
               // Widgets\FilamentInfoWidget::class,
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
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
                CheckForDebug::class,
                SetTheme::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Edit Profile'),
                // ...
            ])
            ->plugins([
                FilamentApexChartsPlugin::make(),
                ThemesPlugin::make()
            ])

            ->navigationItems([
                NavigationItem::make('Analytics')
                    ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-presentation-chart-line')
                    ->group('Reports')
                    ->sort(3),
            ])
            ->profile(isSimple: false)
            ->login()
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s')
            //->topNavigation()
            ->brandLogo(asset('img/logo.png'))
            ->maxContentWidth(MaxWidth::Full)
            ->unsavedChangesAlerts();

    }





}
