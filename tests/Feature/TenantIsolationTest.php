<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\SaasClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_tenant_cannot_see_another_tenants_properties(): void
    {
        $tenant1 = $this->createTenant();
        $tenant2 = $this->createTenant();

        // Create property for tenant1
        DB::statement("SET app.current_tenant_id = '{$tenant1->id}'");
        DB::table('properties')->insert([
            'saas_client_id' => $tenant1->id,
            'name'           => 'Tenant 1 Property',
            'address'        => '123 Main St',
            'status'         => 1,
            'archive'        => 0,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Force fresh DB connection for tenant2 context
        $conn = config('database.default');
        DB::purge($conn);
        DB::reconnect($conn);
        DB::statement("SET app.current_tenant_id = '{$tenant2->id}'");

        $this->assertEquals(0, Property::count());
    }

    public function test_tenant_can_only_see_own_properties(): void
    {
        $tenant = $this->createTenant();

        DB::statement("SET app.current_tenant_id = '{$tenant->id}'");

        DB::table('properties')->insert([
            ['saas_client_id' => $tenant->id, 'name' => 'Property 1', 'address' => '1 St', 'status' => 1, 'archive' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['saas_client_id' => $tenant->id, 'name' => 'Property 2', 'address' => '2 St', 'status' => 1, 'archive' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['saas_client_id' => $tenant->id, 'name' => 'Property 3', 'address' => '3 St', 'status' => 1, 'archive' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->assertEquals(3, Property::count());
    }

    public function test_superadmin_can_see_all_properties(): void
    {
        $tenant1 = $this->createTenant();
        $tenant2 = $this->createTenant();

        DB::statement("SET app.current_tenant_id = '{$tenant1->id}'");
        DB::table('properties')->insert([
            ['saas_client_id' => $tenant1->id, 'name' => 'P1', 'address' => '1 St', 'status' => 1, 'archive' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['saas_client_id' => $tenant1->id, 'name' => 'P2', 'address' => '2 St', 'status' => 1, 'archive' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::statement("SET app.current_tenant_id = '{$tenant2->id}'");
        DB::table('properties')->insert([
            ['saas_client_id' => $tenant2->id, 'name' => 'P3', 'address' => '3 St', 'status' => 1, 'archive' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['saas_client_id' => $tenant2->id, 'name' => 'P4', 'address' => '4 St', 'status' => 1, 'archive' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['saas_client_id' => $tenant2->id, 'name' => 'P5', 'address' => '5 St', 'status' => 1, 'archive' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Superadmin bypasses RLS — sees all 5
        DB::statement('RESET app.current_tenant_id');
        $this->assertEquals(5, Property::count());
    }
}