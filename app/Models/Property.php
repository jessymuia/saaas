<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Property extends DefaultAppModel
{
    protected $fillable = [
        'name',
        'address',
        'description',
        'property_type_id',
        'number_of_units',
        'is_vatable',
        'status',
        'archive',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function propertyType()
    {
        return $this->belongsTo(RefPropertyType::class, 'property_type_id');
    }

    public function propertyOwners()
    {
        return $this->hasMany(PropertyOwners::class, 'property_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'property_id');
    }

    public function unitOccupiedBy()
    {
        // generate relationship for the above defined query
         return $this->hasManyThrough(
             TenancyAgreement::class,
             Unit::class,
             'property_id',
             'unit_id',
             'id',
             'id');

    }

    public function tenancyAgreements()
    {
        return $this->hasManyThrough(
            TenancyAgreement::class,
            Unit::class,
            'property_id',
            'unit_id',
            'id',
            'id');
    }

    public function utilities()
    {
        return $this->hasMany(PropertyUtility::class, 'property_id');
    }

    public function propertyServices()
    {
        return $this->hasMany(PropertyServices::class, 'property_id');
    }

    public function meterReadings()
    {
        return $this->hasManyThrough(
            MeterReading::class,
            Unit::class,
            'property_id',
            'unit_id',
            'id',
            'id');
    }

    public function vacationNotices(){
        return $this->hasMany(VacationNotices::class, 'property_id');
    }

    public function propertyPaymentDetails()
    {
        return $this->hasOne(PropertyPaymentDetails::class, 'property_id');
    }
}
