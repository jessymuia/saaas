<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * BootstrapperTest
 *
 * Validates that the stancl/tenancy bootstrappers are configured correctly:
 *
 *  FilesystemTenancyBootstrapper
 *   - Scopes the local/public disk roots to a tenant-specific subdirectory.
 *   - Does NOT override the asset() URL helper (asset_helper_tenancy = false)
 *     so Filament JS/CSS are served from their original public/ paths, not from
 *     /tenantXXX/js/… paths that would 404 and break Alpine.js.
 *
 *  QueueTenancyBootstrapper (behaviour validated in QueueContextTest)
 *   - Confirmed via: tests/Feature/QueueContextTest.php
 *
 *  CacheTenancyBootstrapper
 *   - Each tenant gets a unique cache prefix so caches never bleed across tenants.
 *
 * Phase 15 checklist: "BootstrapperTest"
 */
class BootstrapperTest extends TestCase
{
    private function makeTenant(): \App\Models\SaasClient
    {
        return \App\Models\SaasClient::make([
            'id'   => (string) Str::uuid(),
            'name' => 'Bootstrapper Test Tenant',
            'slug' => 'bootstrapper-test-' . Str::random(4),
        ]);
    }

    // ── FilesystemTenancyBootstrapper ─────────────────────────────────────────

    public function test_filesystem_bootstrapper_scopes_local_disk_root(): void
    {
        $tenant = $this->makeTenant();

        $baseStorage = app()->storagePath();

        tenancy()->initialize($tenant);

        $localRoot = config('filesystems.disks.local.root');

        $this->assertStringContainsString(
            $tenant->getTenantKey(),
            $localRoot,
            "Local disk root must contain the tenant key after bootstrapping. Got: {$localRoot}"
        );

        tenancy()->end();

        $restoredRoot = config('filesystems.disks.local.root');
        $this->assertStringNotContainsString(
            $tenant->getTenantKey(),
            $restoredRoot ?? '',
            "Local disk root must revert after tenancy ends. Got: {$restoredRoot}"
        );
    }

    public function test_filesystem_bootstrapper_scopes_public_disk_root(): void
    {
        $tenant = $this->makeTenant();

        tenancy()->initialize($tenant);

        $publicRoot = config('filesystems.disks.public.root');

        $this->assertStringContainsString(
            $tenant->getTenantKey(),
            $publicRoot,
            "Public disk root must contain the tenant key after bootstrapping. Got: {$publicRoot}"
        );

        tenancy()->end();
    }

    public function test_asset_helper_is_not_overridden_by_filesystem_bootstrapper(): void
    {
        $tenant = $this->makeTenant();

        $assetBefore = asset('js/filament/filament/app.js');

        tenancy()->initialize($tenant);

        $assetDuring = asset('js/filament/filament/app.js');

        tenancy()->end();

        // With asset_helper_tenancy = false, the asset URL must be identical
        // before and after tenant initialization. If it changes to include the
        // tenant key, Filament JS would be served from a path that doesn't
        // exist, breaking Alpine.js (sidebar, dropdowns, modals all fail).
        $this->assertEquals(
            $assetBefore,
            $assetDuring,
            "asset() must not be modified by FilesystemTenancyBootstrapper when asset_helper_tenancy = false.\n" .
            "Before: {$assetBefore}\nDuring: {$assetDuring}"
        );

        $this->assertStringNotContainsString(
            $tenant->getTenantKey(),
            $assetDuring,
            "asset() URL must not contain the tenant key — Filament assets are global, not per-tenant."
        );
    }

    // ── CacheTenancyBootstrapper ───────────────────────────────────────────────

    public function test_cache_bootstrapper_is_registered(): void
    {
        $bootstrappers = config('tenancy.bootstrappers', []);

        $this->assertContains(
            \Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
            $bootstrappers,
            'CacheTenancyBootstrapper must be listed in config/tenancy.php bootstrappers.'
        );
    }

    public function test_filesystem_bootstrapper_is_registered(): void
    {
        $bootstrappers = config('tenancy.bootstrappers', []);

        $this->assertContains(
            \Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
            $bootstrappers,
            'FilesystemTenancyBootstrapper must be listed in config/tenancy.php bootstrappers.'
        );
    }

    public function test_queue_bootstrapper_is_registered(): void
    {
        $bootstrappers = config('tenancy.bootstrappers', []);

        $this->assertContains(
            \Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
            $bootstrappers,
            'QueueTenancyBootstrapper must be listed in config/tenancy.php bootstrappers.'
        );
    }

    public function test_asset_helper_tenancy_is_disabled(): void
    {
        $this->assertFalse(
            config('tenancy.filesystem.asset_helper_tenancy', true),
            "config('tenancy.filesystem.asset_helper_tenancy') must be false to prevent Filament " .
            "assets from being served at /tenantXXX/ paths that 404 and break Alpine.js."
        );
    }
}
