<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefPropertyType extends DefaultAppModel
{
    protected $fillable = [
        'type',
        'description'
    ];
}
