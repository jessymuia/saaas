<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefPaymentType extends DefaultAppModel
{
    protected $fillable = [
        'type',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];
}
