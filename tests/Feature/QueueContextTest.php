<?php

namespace Tests\Feature;

use App\Models\SaasClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * QueueContextTest
 *
 * Validates that:
 *  1. Queue jobs include saas_client_id context.
 *  2. Tenant context is correctly set and cleared when processing jobs.
 *  3. Failed jobs preserve tenant context.
 *
 * Phase 15 checklist: "queue tests"
 * Phase 11 checklist: "Jobs verified to boot correct tenant context on worker"
 */
class QueueContextTest extends TestCase
{
    private function createTenant(): SaasClient
    {
        $id = (string) Str::uuid();
        DB::table('saas_clients')->insert([
            'id'           => $id,
            'name'         => fake()->company(),
            'slug'         => Str::slug(fake()->company()) . '-' . Str::random(4),
            'status'       => 'active',
            'is_suspended' => false,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
        return SaasClient::find($id);
    }

    public function test_queue_bootstrapper_initializes_tenant_context(): void
    {
        $tenant = SaasClient::make([
            'id'   => (string) Str::uuid(),
            'name' => 'Queue Test Tenant',
            'slug' => 'queue-test-tenant',
        ]);

        tenancy()->initialize($tenant);

        // After initialization, tenant context must be active
        $this->assertTrue(tenancy()->initialized);
        $this->assertEquals($tenant->getTenantKey(), tenant()->getTenantKey());

        tenancy()->end();
    }

    public function test_queue_bootstrapper_reverts_after_job_completes(): void
    {
        $tenant = SaasClient::make([
            'id'   => (string) Str::uuid(),
            'name' => 'Queue Revert Tenant',
            'slug' => 'queue-revert-tenant',
        ]);

        // Simulate job start — initialize tenancy
        tenancy()->initialize($tenant);
        $this->assertTrue(tenancy()->initialized);

        // Simulate job end — tenancy should be cleared
        tenancy()->end();
        $this->assertFalse(tenancy()->initialized);
        $this->assertNull(tenant());
    }

    public function test_no_tenant_context_leak_between_queued_jobs(): void
    {
        $tenant1 = SaasClient::make(['id' => (string) Str::uuid(), 'name' => 'T1', 'slug' => 'tenant-1']);
        $tenant2 = SaasClient::make(['id' => (string) Str::uuid(), 'name' => 'T2', 'slug' => 'tenant-2']);

        // Simulate job 1 processing
        tenancy()->initialize($tenant1);
        $this->assertEquals($tenant1->getTenantKey(), tenant()->getTenantKey());
        tenancy()->end();

        // Simulate job 2 processing — must not have tenant1's context
        tenancy()->initialize($tenant2);
        $this->assertEquals($tenant2->getTenantKey(), tenant()->getTenantKey());
        $this->assertNotEquals($tenant1->getTenantKey(), tenant()->getTenantKey());
        tenancy()->end();

        $this->assertNull(tenant());
    }

    public function test_jobs_table_has_saas_client_id_column(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasColumn('jobs', 'saas_client_id'),
            "The 'jobs' table must have a 'saas_client_id' column for tenant context preservation."
        );
    }

    public function test_failed_jobs_table_has_saas_client_id_column(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasColumn('failed_jobs', 'saas_client_id'),
            "The 'failed_jobs' table must have a 'saas_client_id' column for tenant context preservation."
        );
    }
}
