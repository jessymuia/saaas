<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefBillingType extends DefaultAppModel
{
    protected $fillable = [
        'type',
        'description',
        'frequency_months',
        'due_day',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];
}
