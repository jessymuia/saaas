<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends DefaultAppModel
{
    protected $fillable = [
        'name',
        'address',
        'description',
        'property_type_id',
        'number_of_units',
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

    public function units()
    {
        return $this->hasMany(Unit::class, 'property_id');
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
}
