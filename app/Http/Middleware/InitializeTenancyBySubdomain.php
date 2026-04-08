<?php

namespace App\Http\Middleware;

use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain as BaseMiddleware;
use Illuminate\Support\Facades\DB;

class InitializeTenancyBySubdomain extends BaseMiddleware
{
    public function handle($request, \Closure $next)
    {
        // Skip tenancy init for static Filament assets
        if ($request->is(
            'css/filament/*',
            'js/filament/*',
            'fonts/filament/*',
            'livewire/*',
        )) {
            return $next($request);
        }

        $response = parent::handle($request, $next);

        // Set RLS tenant context after tenancy is initialized
        if (tenancy()->initialized) {
            $tenantId = tenancy()->tenant->id;
            DB::statement("SET app.current_tenant_id = '{$tenantId}'");
        }

        return $response;
    }
}