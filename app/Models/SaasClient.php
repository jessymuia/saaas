<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * SaasClient (Tenant)
 *
 * Single-database multi-tenancy: all tenant data lives in the central DB
 * and is isolated by saas_client_id (row-level). We intentionally do NOT
 * implement TenantWithDatabase / HasDatabase — those are for per-tenant
 * database creation, which is not used in this architecture.
 */
class SaasClient extends Tenant
{
    use HasFactory, HasDomains, Notifiable;

    protected $table = 'saas_clients';

    /**
     * Ensure the JSON data column is always an array, never null, to prevent
     * PHP 8.2 TypeError from array_key_exists() receiving null as second argument.
     */
    protected $attributes = [
        'data' => '{}',
    ];

    /**
     * Columns that physically exist in the DB (not stored in the JSON data column).
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

    // ── Branding helpers (stored in JSON data column) ─────────────────────────

    public function getPrimaryColorAttribute(): ?string
    {
        return $this->data['primary_color'] ?? null;
    }

    public function setPrimaryColorAttribute(string $value): void
    {
        $data = $this->data ?? [];
        $data['primary_color'] = $value;
        $this->data = $data;
    }

    public function getLogoPathAttribute(): ?string
    {
        return $this->data['logo_path'] ?? null;
    }

    public function setLogoPathAttribute(?string $value): void
    {
        $data = $this->data ?? [];
        $data['logo_path'] = $value;
        $this->data = $data;
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
