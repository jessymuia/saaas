<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SaasClientUser extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'saas_client_users';

    protected $fillable = [
        'saas_client_id',
        'user_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function saasClient()
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
