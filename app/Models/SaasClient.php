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
        ];
    }

    protected $casts = [
        'plan_id'       => 'integer',
        'is_suspended'  => 'boolean',
        'suspended_at'  => 'datetime',
    ];

    protected $attributes = [
        'data' => '{}',
    ];

    public function domains(): HasMany
    {
        return $this->hasMany(\App\Models\Domain::class, 'saas_client_id', 'id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Subscription::class, 'saas_client_id');
    }

    public function usageMetric(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\UsageMetric::class, 'saas_client_id');
    }
}