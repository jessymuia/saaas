<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\BelongsToTenant;

class User extends Authenticatable implements \OwenIt\Auditing\Contracts\Auditable, FilamentUser, HasTenants
{
    use BelongsToTenant;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Auditable, HasRoles;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $guarded    = ['id'];

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

    public function transformAudit(array $data): array
    {
        $data['saas_client_id'] = $this->saas_client_id ?? null;
        return $data;
    }

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
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return true;
        }
        return $this->saas_client_id !== null;
    }

    public function getTenants(Panel $panel): Collection
    {
        if ($this->saas_client_id === null) {
            return Collection::make();
        }

        return SaasClient::withoutGlobalScopes()
            ->where('id', $this->saas_client_id)
            ->get();
    }

    public function canAccessTenant(\Illuminate\Database\Eloquent\Model $tenant): bool
    {
        return $this->saas_client_id === $tenant->id;
    }

    public function saasClient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\SaasClient::class, 'saas_client_id');
    }
}
