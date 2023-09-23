<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefBillingType extends DefaultAppModel
{
    protected $fillable = [
        'type',
        'description',
        'frequency',
        'due_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];
}
