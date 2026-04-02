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
    // use the configured domain. Derive the scheme from APP_URL so that
    // http://localhost works locally and https://yourdomain.com works in production.
    $appUrl = config('app.url');
    if ($appUrl) {
        URL::forceRootUrl($appUrl);
        URL::forceScheme(parse_url($appUrl, PHP_URL_SCHEME) ?: 'http');
    }
}
}