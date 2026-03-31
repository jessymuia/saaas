<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetRlsSessionVariables
 *
 * Sets the PostgreSQL session variables that RLS policies read:
 *
 *   Tenant context:  SET app.saas_client_id = '<uuid>'
 *   Central context: SET app.bypass_rls = 'true'
 *
 * This middleware must run AFTER tenancy is initialized so that
 * tenant()->id is available.
 *
 * Registration:
 * - Add to tenant panel middleware stack (AppPanelProvider) AFTER
 *   the tenancy initialization middleware.
 * - Add to central panel middleware stack (AdminPanelProvider) for
 *   super admin bypass.
 */
class SetRlsSessionVariables
{
    public function handle(Request $request, Closure $next): Response
    {
        if (tenancy()->initialized) {
            // ── Tenant context ────────────────────────────────────────
            // Set the saas_client_id so RLS tenant_isolation policies pass.
            $saasClientId = tenant()?->id;

            if ($saasClientId) {
                DB::statement("SET app.saas_client_id = '{$saasClientId}'");
                // Ensure bypass is not accidentally set in tenant context
                DB::statement("RESET app.bypass_rls");
            }
        } else {
            // ── Central / super admin context ─────────────────────────
            // No tenant initialized = central panel = super admin.
            // Set bypass_rls so super_admin_bypass policies pass.
            DB::statement("SET app.bypass_rls = 'true'");
            // Ensure no stale tenant id leaks into central context
            DB::statement("RESET app.saas_client_id");
        }

        $response = $next($request);

        // ── Cleanup after request ─────────────────────────────────────
        // Reset session variables so they don't bleed into pooled connections
        // (important when using PgBouncer in transaction mode).
        DB::statement("RESET app.saas_client_id");
        DB::statement("RESET app.bypass_rls");

        return $response;
    }
}