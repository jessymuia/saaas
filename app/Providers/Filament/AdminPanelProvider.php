<?php

namespace App\Providers\Filament;

use App\Models\SystemAdmin;
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
use \App\Http\Middleware\SetRlsSessionVariables;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            /*
            |------------------------------------------------------------------
            | Phase 9.2 — Central platform uses SystemAdmin guard (local table)
            | NOT the tenant User model. Super admins are isolated from tenants.
            |------------------------------------------------------------------
            */
            ->authGuard('system_admin')
            ->colors([
                'primary' => Color::Indigo,
            ])
            /*
            |------------------------------------------------------------------
            | Phase 10.1 — Discover only Central resources
            |------------------------------------------------------------------
            */
            ->discoverResources(
                in: app_path('Filament/Resources/Central'),
                for: 'App\\Filament\\Resources\\Central'
            )
            ->discoverPages(
                in: app_path('Filament/Pages/Central'),
                for: 'App\\Filament\\Pages\\Central'
            )
            ->discoverWidgets(
                in: app_path('Filament/Widgets/Central'),
                for: 'App\\Filament\\Widgets\\Central'
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
                SetRlsSessionVariables::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
