<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Domain – subdomain and custom domain routing per SaasClient.
 * Central / Local table – NOT distributed.
 *
 * Used by stancl/tenancy to identify which tenant a request belongs to.
 */
class Domain extends Model
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

    // ── Relationships ────────────────────────────────────────────────
    public function saasClient()
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
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