<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckSubscriptionExpiry
 *
 * Redirects tenant users to the billing page when their subscription has
 * expired AND the grace period is also over. Trial tenants and tenants
 * within their grace period pass through normally.
 *
 * Exemptions (always allowed through):
 *  - The billing page itself (prevents redirect loops)
 *  - Logout routes
 *  - Filament asset / livewire endpoints
 */
class CheckSubscriptionExpiry
{
    private const EXEMPT_ROUTE_PREFIXES = [
        'billing',
        'logout',
        'livewire',
        'filament',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = filament()->getTenant();

        if (!$tenant) {
            return $next($request);
        }

        // Skip for exempt paths (billing page itself, logout, etc.)
        $path = $request->path();
        foreach (self::EXEMPT_ROUTE_PREFIXES as $prefix) {
            if (str_contains($path, $prefix)) {
                return $next($request);
            }
        }

        $subscription = $tenant->subscription;

        // No subscription — already handled by CheckTenantSuspended / allow through
        if (!$subscription) {
            return $next($request);
        }

        // If the subscription is expired AND beyond grace period, redirect to billing
        if ($subscription->status === 'expired'
            && $subscription->grace_ends_at
            && now()->isAfter($subscription->grace_ends_at)
        ) {
            $billingUrl = filament()->getPanel()->generateUrl(
                page: \App\Filament\Pages\App\BillingPage::class,
                tenant: $tenant,
            );

            return redirect($billingUrl);
        }

        return $next($request);
    }
}
