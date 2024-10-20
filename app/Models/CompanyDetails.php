<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompanyDetails extends DefaultAppModel
{
    protected $fillable = [
        'name',
        'logo',
        'location', //physical location
        'address', //physical address
        'email',
        'phone_number',
        'account_name',
        'account_number',
        'bank_branch',
        'bank_name',
        'branch_swift_code',
        'mpesa_paybill_number',
        'status',
        'archive',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function () {
            static::query()->delete();
        });

        static::created(function ($model) {
            \Log::info('New record created with ID: ' . $model->id);
        });
    }
}
