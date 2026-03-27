<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaasClient extends Tenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $table = 'saas_clients';

   
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'slug',
            'plan_id',
            'status'
        ];
    }

    protected $casts = [
        'data'    => 'array',
        'plan_id' => 'integer',
    ];


    protected $attributes = [
        'data' => '{}',
    ];

    
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, 'saas_client_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}