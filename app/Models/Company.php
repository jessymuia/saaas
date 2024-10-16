<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Company extends DefaultAppModel
{
    protected $fillable = [
        'name',
        'logo',
        'address', //physical address
        'email',
        'account_name',
        'account_number',
        'bank_branch',
        'branch_swift_code',
        'status',
        'archive',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
