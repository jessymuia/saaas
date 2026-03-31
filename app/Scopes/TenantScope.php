<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * TenantScope
 *
 * Automatically restricts Eloquent queries to the current tenant's records
 * by filtering on the `saas_client_id` column when tenancy is initialized.
 *
 * Applied globally via the BelongsToTenant trait.
 * Use `Model::withoutGlobalScope(TenantScope::class)` to bypass when needed.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (tenancy()->initialized) {
            $tenantId = tenant()?->id;

            if ($tenantId !== null) {
                $builder->where($model->getTable() . '.saas_client_id', $tenantId);
            }
        }
    }
}
