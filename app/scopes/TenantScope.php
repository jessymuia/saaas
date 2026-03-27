<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;


class TenantScope implements Scope
{
    
    public function apply(Builder $builder, Model $model): void
    {
        
        if (!tenancy()->initialized) {
            return;
        }

        $saasClientId = tenant()?->id;

        if ($saasClientId === null) {
            return;
        }

        $builder->where(
            $model->getTable() . '.saas_client_id',
            '=',
            $saasClientId
        );
    }
}
