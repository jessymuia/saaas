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
        'password' => 'hashed',
        'status' => 'boolean',
        'archive' => 'boolean',
    ];

    /**
     * Requirement for Citus: Inject Tenant ID into Audit
     * This ensures the 'audits' table gets the partition key it needs.
     */
    public function transformAudit(array $data): array
    {
        // If the user has no tenant (Central Admin), we use 0
        $data['saas_client_id'] = $this->saas_client_id ?? 0;
        
        return $data;
    }

    /**
     * Citus Logic: Only set tenant ID on creation, never update it.
     */
    public function setSaasClientIdAttribute($value)
    {
        if (!$this->exists) {
            $this->attributes['saas_client_id'] = $value;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
            // Strip partition key from update strings to avoid Citus errors
            unset($model->saas_client_id);
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Central Admin Panel
        if ($panel->getId() === 'admin') {
            return true;
        }
        // Tenant Panels
        return $this->saas_client_id !== null;
    }
    public function saasClient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
{
    return $this->belongsTo(\App\Models\SaasClient::class, 'saas_client_id');
}
}