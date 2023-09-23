<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends DefaultAppModel
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];
}
