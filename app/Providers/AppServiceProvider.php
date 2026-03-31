<?php

namespace App\Providers;

use App\Auth\TenantUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
{
    // Register our custom provider that bypasses TenantScope during auth
    Auth::provider('tenant_eloquent', function ($app, array $config) {
        return new TenantUserProvider($app['hash'], $config['model']);
    });

    // Only super admins can access the Horizon dashboard
    Horizon::auth(function ($request) {
        return $request->user()?->is_super_admin === true;
    });

    // Always force HTTPS — Replit (and production) terminate SSL at the proxy layer
    URL::forceScheme('https');
}
}