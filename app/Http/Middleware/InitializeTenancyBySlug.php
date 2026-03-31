<?php

namespace App\Http\Middleware;

use App\Models\SaasClient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Tenancy;

/**
 * InitializeTenancyBySlug
 *
 * Identifies the tenant from the URL path segment (e.g. /app/{slug}/...).
 * Used instead of InitializeTenancyBySubdomain because Replit does not
 * support subdomain routing.
 */
class InitializeTenancyBySlug
{
    public function __construct(protected Tenancy $tenancy) {}

    public function handle(Request $request, Closure $next)
    {
        // Skip tenancy init for static Filament assets and Livewire
        if ($request->is('css/filament/*', 'js/filament/*', 'fonts/filament/*', 'livewire/*')) {
            return $next($request);
        }

        // URL pattern: /app/app/{slug}/...
        // (panel path = 'app', tenantRoutePrefix = 'app', then slug)
        // segment(1) = 'app', segment(2) = 'app', segment(3) = slug
        // Fall back to segment(2) in case the prefix is removed later.
        $slug = $request->segment(3) ?? $request->segment(2);

        if ($slug) {
            $tenant = SaasClient::where('slug', $slug)->first();

            if ($tenant && !$this->tenancy->initialized) {
                $this->tenancy->initialize($tenant);

                // Set RLS session variable so tenant-isolation policies pass
                if ($this->tenancy->initialized) {
                    DB::statement("SET app.saas_client_id = '{$tenant->id}'");
                }
            }
        }

        return $next($request);
    }
}
