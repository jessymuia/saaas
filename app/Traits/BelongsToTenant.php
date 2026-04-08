<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;


trait BelongsToTenant
{
    
    public static function bootBelongsToTenant(): void
    {
       
        static::addGlobalScope(new TenantScope());

        
        static::creating(function ($model) {
            if (tenancy()->initialized && $model->saas_client_id === null) {
                $model->saas_client_id = tenant()?->id;
            }
        });
    }

    
    public static function withoutTenantScope(): Builder
    {
        return static::withoutGlobalScope(TenantScope::class);
    }

   
    public static function forTenant(int $saasClientId): Builder
    {
        return static::withoutGlobalScope(TenantScope::class)
            ->where('saas_client_id', $saasClientId);
    }
}