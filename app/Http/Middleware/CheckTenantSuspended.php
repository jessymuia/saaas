<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTenantSuspended
{
    public function handle(Request $request, Closure $next)
    {
        if (tenancy()->initialized && tenancy()->tenant->is_suspended) {
            abort(403, 'Your account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}