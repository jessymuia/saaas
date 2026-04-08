<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaasClientUser extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'saas_client_users';

    protected $fillable = [
        'saas_client_id',
        'user_id',
        'role',
        'version',
        'status',
        'archive',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'archive'    => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function saasClient(): BelongsTo
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
}
