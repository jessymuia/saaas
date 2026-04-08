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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\CheckSubscriptionExpiry;
use App\Http\Middleware\CheckTenantSuspended;
use App\Http\Middleware\InitializeTenancyBySlug;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use App\Http\Middleware\SetRlsSessionVariables;
use App\Http\Middleware\TenantBrandingMiddleware;


class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->login()
            ->passwordReset()
            ->authPasswordBroker('users')
            ->authGuard('web')
            ->tenant(SaasClient::class, slugAttribute: 'slug')
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
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                // Switch between subdomain (production) and slug (dev/Replit) identification
                // via APP_TENANT_MODE=subdomain|slug in .env
                ...(config('app.tenant_mode', 'slug') === 'subdomain'
                    ? [InitializeTenancyBySubdomain::class]
                    : [InitializeTenancyBySlug::class]),
                CheckTenantSuspended::class,
                SetRlsSessionVariables::class,
                TenantBrandingMiddleware::class,
                CheckSubscriptionExpiry::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                SetRlsSessionVariables::class,
            ]);
    }
}