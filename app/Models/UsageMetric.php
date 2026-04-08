<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsageMetric extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'saas_client_id',
        'properties_count',
        'units_count',
        'users_count',
        'tenants_count',
        'invoices_count',
        'storage_used_kb',
        'last_calculated_at',
        'version',
        'status',
        'archive',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'last_calculated_at' => 'datetime',
        'status'             => 'boolean',
        'archive'            => 'boolean',
        'deleted_at'         => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
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

    public function isWithinPlanLimits(Plan $plan): bool
    {
        if ($plan->max_properties !== -1 && $this->properties_count >= $plan->max_properties) return false;
        if ($plan->max_units      !== -1 && $this->units_count      >= $plan->max_units)      return false;
        if ($plan->max_users      !== -1 && $this->users_count      >= $plan->max_users)      return false;
        return true;
    }
}
