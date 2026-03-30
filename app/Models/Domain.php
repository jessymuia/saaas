<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Models\Domain as StanclDomain;

class Domain extends StanclDomain
{
    use HasFactory;

    protected $table = 'domains';

    protected $fillable = [
        'saas_client_id',
        'domain',
        'type',
        'is_primary',
        'is_verified',
    ];

    protected $casts = [
        'is_primary'  => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function saasClient()
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
    }
    public function tenant()
{
    return $this->belongsTo(config('tenancy.tenant_model'), 'saas_client_id');
}

    // ── Helpers ──────────────────────────────────────────────────────
    public function isSubdomain(): bool
    {
        return $this->type === 'subdomain';
    }

    public function isCustom(): bool
    {
        return $this->type === 'custom';
    }

    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    // ── Scopes ───────────────────────────────────────────────────────
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }   // ← was missing this closing brace in original

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeSubdomains($query)
    {
        return $query->where('type', 'subdomain');
    }

    public function scopeCustom($query)
    {
        return $query->where('type', 'custom');
    }
}