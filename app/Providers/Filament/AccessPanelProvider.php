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
use Filament\FontProviders\GoogleFontProvider;
use Archilex\AdvancedTables\Plugin\AdvancedTablesPlugin;
use App\Filament\Pages\Tickets;
use App\Filament\Pages\OverviewDashboard;
use App\Filament\Pages\PaymentsDashboard;
use App\Filament\Pages\WalletsDashboard;
use App\Filament\Pages\TicketsDashboard;
use App\Filament\Pages\UsersDashboard;

class AccessPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('access')
            ->path('access')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->font('Vazirmatn', provider: GoogleFontProvider::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Main Dashboard Pages
                OverviewDashboard::class,
                PaymentsDashboard::class,
                WalletsDashboard::class,
                TicketsDashboard::class,
                UsersDashboard::class,
                
                // Other Pages
                Tickets::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->plugins([
                AdvancedTablesPlugin::make()
                    ->userViewsEnabled(false)
                    ->resourceEnabled(false)
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
            ->viteTheme(['resources/css/filament/access/theme.css' , 'resources/js/filament/access/app.js'] , 'panel')
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarFullyCollapsibleOnDesktop()
            ;
    }
}
