<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\TenantScope;
use App\Traits\BelongsToTenant;

/**
 * Provides a track record of the monthly occupation of a unit
 * Act as source of truth for the monthly occupation of a unit to help
 * in the identification of rent/lease without a tenancy bill
 * Class UnitOccupationMonthlyRecords
 */
class UnitOccupationMonthlyRecords extends DefaultAppModel
{
    use BelongsToTenant;
    protected $fillable = [
        'unit_id',
        'tenancy_agreement_id',
        'from_date',
        'end_date',
        'tenancy_bill_id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
        'status',
        'archive',
        'saas_client_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);

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

    protected $casts = [
        'from_date' => 'date',
        'end_date' => 'date'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenancyAgreement()
    {
        return $this->belongsTo(TenancyAgreement::class);
    }

    public function tenancyBill()
    {
        return $this->belongsTo(TenancyBill::class);
    }
}
