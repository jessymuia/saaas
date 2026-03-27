<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscalationRatesAndAmountsLogs extends DefaultAppModel
{
    use BelongsToTenant;

    protected $fillable = [
        'tenancy_agreement_id',
        'property_id',
        'escalation_rate',
        'previous_amount',
        'new_amount',
        'escalation_date',
        'status',
        'archive',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
        'saas_client_id',           // ← Added this to satisfy the fillable test
    ];

    public function tenancyAgreement()
    {
        return $this->belongsTo(TenancyAgreement::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TenantScope);  // ← Added this to register TenantScope globally

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
}