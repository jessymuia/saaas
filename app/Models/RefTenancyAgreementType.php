<?php

namespace App\Models;

use App\Utils\AppUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefTenancyAgreementType extends DefaultAppModel
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
