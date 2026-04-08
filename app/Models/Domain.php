<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Models\Domain as StanclDomain;

class Domain extends StanclDomain
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'domains';

    protected $fillable = [
        'saas_client_id',
        'domain',
        'type',
        'is_primary',
        'is_verified',
        'version',
        'status',
        'archive',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_primary'  => 'boolean',
        'is_verified' => 'boolean',
        'status'      => 'boolean',
        'archive'     => 'boolean',
        'deleted_at'  => 'datetime',
    ];

    public function saasClient(): BelongsTo
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(config('tenancy.tenant_model'), 'saas_client_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ── Helpers ──────────────────────────────────────────────────────
    public function isSubdomain(): bool { return $this->type === 'subdomain'; }
    public function isCustom(): bool    { return $this->type === 'custom'; }
    public function isPrimary(): bool   { return $this->is_primary; }
    public function isVerified(): bool  { return $this->is_verified; }

    // ── Scopes ───────────────────────────────────────────────────────
    public function scopePrimary($query)    { return $query->where('is_primary', true); }
    public function scopeVerified($query)   { return $query->where('is_verified', true); }
    public function scopeSubdomains($query) { return $query->where('type', 'subdomain'); }
    public function scopeCustom($query)     { return $query->where('type', 'custom'); }
}
