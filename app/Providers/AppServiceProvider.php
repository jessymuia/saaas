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

    // Force the full root URL so all generated URLs (assets, routes, etc.)
    // use the public domain instead of the internal localhost address.
    // Replit terminates SSL at the proxy layer, so we also force HTTPS.
    $appUrl = config('app.url');
    if ($appUrl) {
        URL::forceRootUrl($appUrl);
    }
    URL::forceScheme('https');
}
}