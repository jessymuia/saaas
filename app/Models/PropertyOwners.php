<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyOwners extends DefaultAppModel
{
    protected $fillable = [
        'property_id',
        'name',
        'email',
        'phone_number',
        'address',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
