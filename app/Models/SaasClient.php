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

    // ── Branding helpers (stored in JSON data column via VirtualColumn) ──────────
    //
    // VirtualColumn decodes the `data` JSON column into individual model attributes
    // on retrieval and re-encodes them back into `data` on save. We must work with
    // the individual attribute key (not set $this->data directly) so that
    // encodeAttributes() picks up the change and persists it correctly.

    public function getPrimaryColorAttribute(): ?string
    {
        return $this->attributes['primary_color'] ?? null;
    }

    public function setPrimaryColorAttribute(?string $value): void
    {
        $this->attributes['primary_color'] = $value;
    }

    public function getLogoPathAttribute(): ?string
    {
        return $this->attributes['logo_path'] ?? null;
    }

    public function setLogoPathAttribute(?string $value): void
    {
        $this->attributes['logo_path'] = $value;
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
