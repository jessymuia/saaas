<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\BelongsToTenant;

class User extends Authenticatable implements \OwenIt\Auditing\Contracts\Auditable, FilamentUser
{
    use BelongsToTenant;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Auditable, HasRoles;

    protected $guarded = ['id'];
    protected $primaryKey = 'id';

    protected $fillable = [
        'saas_client_id',
        'name',
        'email',
        'phone_number',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'status'            => 'boolean',
        'archive'           => 'boolean',
    ];

    /**
     * Inject Tenant ID into Audit for Citus partition key.
     */
    public function transformAudit(array $data): array
    {
        $data['saas_client_id'] = $this->saas_client_id ?? 0;
        return $data;
    }

    /**
     * Only set saas_client_id on creation — Citus partition key cannot change.
     */
    public function setSaasClientIdAttribute($value): void
    {
        if (! $this->exists) {
            $this->attributes['saas_client_id'] = $value;
        }
    }

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
            // Do NOT unset saas_client_id here — it breaks the Citus partition key
            // on UPDATE queries. Instead, handle this at the DB level via your
            // migration (the column should be immutable by design).
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return true;
        }
        return $this->saas_client_id !== null;
    }

    public function saasClient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\SaasClient::class, 'saas_client_id');
    }
}