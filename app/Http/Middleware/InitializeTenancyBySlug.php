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
        // Skip tenancy init for static Filament assets only
        if ($request->is('css/filament/*', 'js/filament/*', 'fonts/filament/*')) {
            return $next($request);
        }

        // For Livewire update requests, extract the slug from the Referer header
        // because the URL itself (/livewire/update) doesn't contain the tenant slug.
        if ($request->is('livewire/*')) {
            $referer = $request->headers->get('referer', '');
            $slug = $this->extractSlugFromUrl($referer);
        } else {
            // URL pattern: /app/{slug}/...
            // segment(1) = 'app', segment(2) = slug
            $slug = $request->segment(2);
        }

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

    /**
     * Extract the tenant slug from a full URL string.
     * Expects pattern: /app/{slug}/...
     */
    private function extractSlugFromUrl(string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $segments = array_values(array_filter(explode('/', $path)));

        // segments: [0]=app, [1]=slug, ...
        return $segments[1] ?? null;
    }
}
