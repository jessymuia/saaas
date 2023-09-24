<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefUtility extends DefaultAppModel
{
    protected $fillable = [
        'name',
        'description',
        'unit_of_measurement',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    public function propertyUtilities()
    {
        return $this->hasMany(PropertyUtility::class, 'utility_id');
    }
}
