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
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
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

    public function users()
    {
        return $this->belongsToMany(User::class, 'property_management_users', 'property_id', 'user_id')
            ->withPivot('status', 'role_id');
    }
}
