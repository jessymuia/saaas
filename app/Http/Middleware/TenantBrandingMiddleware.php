<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * TenantBrandingMiddleware
 *
 * Applies per-tenant branding to the Filament App panel at runtime.
 * Reads primary_color (hex) from the tenant's JSON data column and
 * generates a full Filament-compatible colour shades array via PHP.
 *
 * Color is stored as a hex string: "#3b82f6".
 * Logo is served directly from the public disk and injected via the view.
 */
class TenantBrandingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = filament()->getTenant();

        if ($tenant) {
            $this->applyColor($tenant->data['primary_color'] ?? null);
        }

        return $next($request);
    }

    /**
     * Convert a hex colour to a Filament-compatible 50–950 shade array
     * and register it as the panel's 'primary' colour.
     */
    private function applyColor(?string $hex): void
    {
        if (blank($hex)) {
            return;
        }

        $hex = ltrim($hex, '#');
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return;
        }

        [$r, $g, $b] = sscanf($hex, '%02x%02x%02x');

        // Build a basic 10-step palette centred around the provided colour.
        // For production-quality shading, replace with a full palette generator.
        $shades = $this->generateShades($r, $g, $b);

        Filament::getCurrentPanel()?->colors([
            'primary' => $shades,
        ]);
    }

    /**
     * Generate a simple 50–950 shade map from a base RGB colour.
     * Lighter shades blend toward white; darker shades blend toward black.
     */
    private function generateShades(int $r, int $g, int $b): array
    {
        $steps = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];
        $shades = [];

        foreach ($steps as $step) {
            // 500 = base colour; <500 = lighter; >500 = darker
            $factor = ($step - 500) / 1000;          // -0.45 … +0.45
            $blendR = (int) max(0, min(255, $r - ($factor * 255)));
            $blendG = (int) max(0, min(255, $g - ($factor * 255)));
            $blendB = (int) max(0, min(255, $b - ($factor * 255)));

            $shades[$step] = "rgb({$blendR},{$blendG},{$blendB})";
        }

        return $shades;
    }
}
