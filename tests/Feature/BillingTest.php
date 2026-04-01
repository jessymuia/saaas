<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\SaasClient;
use App\Models\Subscription;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * BillingTest
 *
 * Tests the subscription lifecycle, amount calculation, and BillingService
 * behaviour for PropManage SaaS.
 *
 * Phase 15 checklist: "BillingTest"
 */
class BillingTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function createPlan(array $overrides = []): Plan
    {
        return Plan::create(array_merge([
            'name'           => 'Starter',
            'slug'           => 'starter-' . Str::random(4),
            'description'    => 'Test plan',
            'price_monthly'  => 1500.00,
            'price_yearly'   => 15000.00,
            'max_properties' => 10,
            'max_units'      => 50,
            'max_users'      => 5,
            'is_active'      => true,
        ], $overrides));
    }

    private function createTenant(int $planId): SaasClient
    {
        $id = (string) Str::uuid();
        DB::table('saas_clients')->insert([
            'id'           => $id,
            'name'         => fake()->company(),
            'slug'         => Str::slug(fake()->company()) . '-' . Str::random(4),
            'plan_id'      => $planId,
            'status'       => 'trial',
            'is_suspended' => false,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
        return SaasClient::find($id);
    }

    private function createSubscription(SaasClient $tenant, Plan $plan, array $overrides = []): Subscription
    {
        return Subscription::create(array_merge([
            'saas_client_id' => $tenant->id,
            'plan_id'        => $plan->id,
            'status'         => 'trialing',
            'billing_cycle'  => 'monthly',
            'starts_at'      => now(),
            'ends_at'        => now()->addDays(14),
            'trial_ends_at'  => now()->addDays(14),
            'reminder_count' => 0,
        ], $overrides));
    }

    // ── Subscription::getAmount() ─────────────────────────────────────────────

    public function test_get_amount_returns_monthly_price_for_monthly_cycle(): void
    {
        $plan         = $this->createPlan(['price_monthly' => 1500.00]);
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan, ['billing_cycle' => 'monthly']);

        $this->assertEquals(1500.00, $subscription->getAmount());
    }

    public function test_get_amount_returns_yearly_price_for_annual_cycle(): void
    {
        $plan         = $this->createPlan(['price_yearly' => 15000.00]);
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan, ['billing_cycle' => 'annual']);

        $this->assertEquals(15000.00, $subscription->getAmount());
    }

    public function test_get_amount_returns_quarterly_price(): void
    {
        $plan         = $this->createPlan(['price_yearly' => 12000.00]);
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan, ['billing_cycle' => 'quarterly']);

        $this->assertEquals(3000.00, $subscription->getAmount());
    }

    public function test_get_amount_returns_zero_when_no_plan(): void
    {
        $plan         = $this->createPlan();
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan);

        // Detach the plan to simulate missing relationship
        $subscription->plan_id = null;
        $subscription->setRelation('plan', null);

        $this->assertEquals(0.0, $subscription->getAmount());
    }

    // ── Subscription status lifecycle ─────────────────────────────────────────

    public function test_subscription_is_trialing_when_status_is_trialing_and_future_end(): void
    {
        $plan         = $this->createPlan();
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan, [
            'status'        => 'trialing',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $this->assertTrue($subscription->isTrialing());
    }

    public function test_subscription_is_not_trialing_after_trial_ends(): void
    {
        $plan         = $this->createPlan();
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan, [
            'status'        => 'trialing',
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->assertFalse($subscription->isTrialing());
    }

    public function test_subscription_is_active_when_status_active_and_future_end(): void
    {
        $plan         = $this->createPlan();
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan, [
            'status'  => 'active',
            'ends_at' => now()->addMonth(),
        ]);

        $this->assertTrue($subscription->isActive());
    }

    public function test_subscription_is_expired_when_ends_at_is_past(): void
    {
        $plan         = $this->createPlan();
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan, [
            'status'  => 'active',
            'ends_at' => now()->subDay(),
        ]);

        $this->assertTrue($subscription->isExpired());
    }

    public function test_activate_from_trial_changes_status_to_active(): void
    {
        $plan         = $this->createPlan();
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan);

        $subscription->activateFromTrial();
        $subscription->refresh();

        $this->assertEquals('active', $subscription->status);
        $this->assertNull($subscription->trial_ends_at);
        $this->assertTrue($subscription->ends_at->isFuture());
    }

    public function test_renew_extends_subscription_by_one_month(): void
    {
        $plan         = $this->createPlan();
        $tenant       = $this->createTenant($plan->id);
        $originalEnd  = now()->addDays(3);

        $subscription = $this->createSubscription($tenant, $plan, [
            'status'         => 'active',
            'billing_cycle'  => 'monthly',
            'ends_at'        => $originalEnd,
        ]);

        $subscription->renew();
        $subscription->refresh();

        $this->assertEquals('active', $subscription->status);
        $this->assertTrue($subscription->ends_at->isAfter($originalEnd));
    }

    public function test_move_to_grace_period_sets_correct_status(): void
    {
        $plan         = $this->createPlan();
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan, [
            'status'  => 'active',
            'ends_at' => now()->subDay(),
        ]);

        $subscription->moveToGracePeriod(7);
        $subscription->refresh();

        $this->assertEquals('grace_period', $subscription->status);
        $this->assertNotNull($subscription->grace_ends_at);
        $this->assertTrue($subscription->grace_ends_at->isFuture());
    }

    // ── BillingService ────────────────────────────────────────────────────────

    public function test_billing_service_creates_renewal_invoice(): void
    {
        $plan         = $this->createPlan();
        $tenant       = $this->createTenant($plan->id);
        $subscription = $this->createSubscription($tenant, $plan, ['status' => 'active']);
        $subscription->load(['plan', 'saasClient']);

        $result = BillingService::createRenewalInvoice($subscription);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('invoice', $result);
        $this->assertEquals($tenant->id, $result['invoice']['saas_client_id']);
        $this->assertEquals($plan->price_monthly, $result['invoice']['amount']);
    }

    // ── Tenant branding (data column) ─────────────────────────────────────────

    public function test_saas_client_stores_and_retrieves_primary_color(): void
    {
        $plan   = $this->createPlan();
        $tenant = $this->createTenant($plan->id);

        $tenant->setPrimaryColorAttribute('#3b82f6');
        $tenant->save();
        $tenant->refresh();

        $this->assertEquals('#3b82f6', $tenant->getPrimaryColorAttribute());
    }

    public function test_saas_client_stores_and_retrieves_logo_path(): void
    {
        $plan   = $this->createPlan();
        $tenant = $this->createTenant($plan->id);

        $tenant->setLogoPathAttribute('tenant-logos/test-logo.png');
        $tenant->save();
        $tenant->refresh();

        $this->assertEquals('tenant-logos/test-logo.png', $tenant->getLogoPathAttribute());
    }

    public function test_primary_color_is_null_when_not_set(): void
    {
        $plan   = $this->createPlan();
        $tenant = $this->createTenant($plan->id);

        $this->assertNull($tenant->getPrimaryColorAttribute());
    }
}
