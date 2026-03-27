<?php

namespace Tests\Unit\Tenancy;

use App\Models\Client;
use App\Models\CreditNote;
use App\Models\EmailAttachments;
use App\Models\EscalationRatesAndAmountsLogs;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\ManualInvoiceItem;
use App\Models\ManualInvoices;
use App\Models\MeterReading;
use App\Models\Property;
use App\Models\PropertyManagementUsers;
use App\Models\PropertyOwners;
use App\Models\PropertyPaymentDetails;
use App\Models\PropertyServices;
use App\Models\PropertyUtility;
use App\Models\SentEmails;
use App\Models\SaasClient;
use App\Models\TenancyAgreement;
use App\Models\TenancyAgreementFiles;
use App\Models\TenancyBill;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitOccupationMonthlyRecords;
use App\Models\User;
use App\Models\VacationNotices;
use App\Scopes\TenantScope;
use App\Traits\BelongsToTenant;
use Tests\TestCase;


class TenantScopeTest extends TestCase
{
    /**
     * List of all distributed models that must have BelongsToTenant.
     */
    private function distributedModels(): array
    {
        return [
            Client::class,
            CreditNote::class,
            EmailAttachments::class,
            EscalationRatesAndAmountsLogs::class,
            Invoice::class,
            InvoicePayment::class,
            ManualInvoiceItem::class,
            ManualInvoices::class,
            MeterReading::class,
            Property::class,
            PropertyManagementUsers::class,
            PropertyOwners::class,
            PropertyPaymentDetails::class,
            PropertyServices::class,
            PropertyUtility::class,
            SentEmails::class,
            TenancyAgreement::class,
            TenancyAgreementFiles::class,
            TenancyBill::class,
            Tenant::class,
            Unit::class,
            UnitOccupationMonthlyRecords::class,
            User::class,
            VacationNotices::class,
        ];
    }

   
    public function test_all_distributed_models_use_belongs_to_tenant_trait(): void
    {
        foreach ($this->distributedModels() as $modelClass) {
            $traits = class_uses_recursive($modelClass);
            $this->assertArrayHasKey(
                BelongsToTenant::class,
                $traits,
                "{$modelClass} is missing the BelongsToTenant trait."
            );
        }
    }

    
    public function test_all_distributed_models_have_tenant_scope_registered(): void
    {
        foreach ($this->distributedModels() as $modelClass) {
            $instance = new $modelClass();
            $scopes   = $instance->getGlobalScopes();
            $this->assertArrayHasKey(
                TenantScope::class,
                $scopes,
                "{$modelClass} does not have TenantScope registered as a global scope."
            );
        }
    }

    
    public function test_all_distributed_models_have_saas_client_id_fillable(): void
    {
        foreach ($this->distributedModels() as $modelClass) {
            $instance = new $modelClass();

           
            $guarded = $instance->getGuarded();
            if (in_array('*', $guarded)) {
               
                $this->fail("{$modelClass} is fully guarded — saas_client_id cannot be mass assigned.");
            }

            $fillable = $instance->getFillable();
            if (!empty($fillable)) {
             
                $this->assertContains(
                    'saas_client_id',
                    $fillable,
                    "{$modelClass} has explicit \$fillable but saas_client_id is not in it."
                );
            }
            
        }
    }

    /**
     * Test: withoutTenantScope() removes TenantScope from the query.
     */
    public function test_without_tenant_scope_removes_scope(): void
    {
        foreach ($this->distributedModels() as $modelClass) {
            $query  = $modelClass::withoutTenantScope();
            $scopes = $query->getScopes();

            $this->assertArrayNotHasKey(
                TenantScope::class,
                $scopes,
                "{$modelClass}::withoutTenantScope() did not remove TenantScope."
            );
        }
    }

    /**
     * Test: forTenant() scopes query to a specific saas_client_id.
     */
    public function test_for_tenant_scopes_to_specific_tenant(): void
    {
        $targetId = 42;

        foreach ($this->distributedModels() as $modelClass) {
            $query = $modelClass::forTenant($targetId);
            $sql   = $query->toSql();

            $this->assertStringContainsString(
                'saas_client_id',
                $sql,
                "{$modelClass}::forTenant() did not add saas_client_id to query."
            );
        }
    }

    /**
     * Test: TenantScope does NOT apply when tenancy is not initialized.
     * This ensures central/admin context works without tenant context.
     */
    public function test_scope_does_not_apply_when_tenancy_not_initialized(): void
    {
        
        tenancy()->end();
        $this->assertFalse(tenancy()->initialized);

        foreach ($this->distributedModels() as $modelClass) {
            $sql = $modelClass::query()->toSql();
            
            $this->assertStringNotContainsString(
                'saas_client_id = ?',
                $sql,
                "{$modelClass} injected saas_client_id even when tenancy is not initialized."
            );
        }
    }

    /**
     * Test: TenantScope injects saas_client_id when tenancy IS initialized.
     */
    public function test_scope_injects_saas_client_id_when_tenancy_initialized(): void
    {
        $tenant = SaasClient::make(['id' => 99, 'name' => 'Test', 'slug' => 'test']);
        tenancy()->initialize($tenant);

        foreach ($this->distributedModels() as $modelClass) {
            $sql = $modelClass::query()->toSql();
            $this->assertStringContainsString(
                'saas_client_id',
                $sql,
                "{$modelClass} did not inject saas_client_id when tenancy was initialized."
            );
        }

        tenancy()->end();
    }
}
