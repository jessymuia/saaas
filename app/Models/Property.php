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
}
