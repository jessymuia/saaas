<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements \OwenIt\Auditing\Contracts\Auditable, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes,Auditable, HasRoles;

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'archive' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'created_by' => 'integer',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'updated_by' => 'integer',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
        'deleted_by' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->created_by = auth()->id();
            $model->saveQuietly();
        });

        static::updated(function ($model) {
            $model->updated_by = auth()->id();
            $model->saveQuietly();
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->id();
            $model->deleted_at = now();
            $model->save();
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class,'deleted_by');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // allow all users to access
        return true;
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_management_users',  'user_id', 'property_id')
            ->withPivot('status', 'role_id');
    }
}
