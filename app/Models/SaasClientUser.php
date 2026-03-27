<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * SaasClientUser — platform super admin users only.
 * NOT the property manager users inside each tenant.
 * Property manager users live in the distributed `users` table.
 *
 * Central / Local table — NOT distributed.
 */
class SaasClientUser extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'saas_client_users';

    protected $fillable = [
        'saas_client_id',
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function saasClient()
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isSupport(): bool
    {
        return $this->role === 'support';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSuperAdmins($query)
    {
        return $query->where('role', 'super_admin');
    }
}
