<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends DefaultAppModel
{
    protected $fillable = [
        'property_id',
        'name',
        'unit_type_id',
        'area_in_square_feet',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
        'status',
        'archive'
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

    // foreign keys
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function unitType()
    {
        return $this->belongsTo(RefUnitType::class);
    }

    public function tenancyAgreements()
    {
        return $this->hasMany(TenancyAgreement::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class);
    }
}
