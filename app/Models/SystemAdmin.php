<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class SystemAdmin extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $guard      = 'system_admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'version',
        'status',
        'archive',
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
        'deleted_at'        => 'datetime',
    ];
}
