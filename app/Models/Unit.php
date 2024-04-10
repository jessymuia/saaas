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
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

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
