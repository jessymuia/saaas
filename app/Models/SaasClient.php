<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class SaasClient extends Tenant implements TenantWithDatabase
{
    use HasFactory, HasDatabase, HasDomains, Notifiable;

    protected $table = 'saas_clients';

    /**
     * Ensure the JSON data column is always an array, never null, to prevent
     * PHP 8.2 TypeError from array_key_exists() receiving null as second argument
     * (triggered by VirtualColumn trait during Livewire form hydration on Create pages).
     */
    protected $attributes = [
        'data' => '{}',
    ];

    /**
     * Columns that physically exist in DB
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'slug',
            'plan_id',
            'status',
            'email',
            'phone',
            'contact_name',
            'is_suspended',
            'suspended_at',
            'suspension_reason',
            'created_at',
            'updated_at',
        ];
    }

    protected function casts(): array
    {
        return [
            'plan_id'      => 'integer',
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
            'data'         => 'array',
        ];
    }

    public static function getDataColumn(): string
    {
        return 'data';
    }

    public function domains(): HasMany
    {
        return $this->hasMany(\App\Models\Domain::class, 'saas_client_id', 'id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscription()
    {
        return $this->hasOne(\App\Models\Subscription::class, 'saas_client_id');
    }

    public function usageMetric()
    {
        return $this->hasOne(\App\Models\UsageMetric::class, 'saas_client_id');
    }
}