<?php

namespace App\Providers\Filament;

use App\Models\SaasClient;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use App\Http\Middleware\CheckTenantSuspended;


class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->login()
            ->authGuard('web')
            ->tenant(SaasClient::class, slugAttribute: 'slug')
            ->tenantRoutePrefix('app')
            ->colors(['primary' => Color::Emerald])
            ->discoverResources(
                in: app_path('Filament/Resources/App'),
                for: 'App\\Filament\\Resources\\App'
            )
            ->discoverPages(
                in: app_path('Filament/Pages/App'),
                for: 'App\\Filament\\Pages\\App'
            )
            ->discoverWidgets(
                in: app_path('Filament/Widgets/App'),
                for: 'App\\Filament\\Widgets\\App'
            )
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
                InitializeTenancyBySubdomain::class,
                CheckTenantSuspended::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}