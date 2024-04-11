<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends DefaultAppModel
{
    protected $fillable = [
        'name',
        'description',
        'is_area_based_service',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];
}
