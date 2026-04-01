<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'deleted_by',
        'deleted_at'
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            \Log::info('New record created with ID: ' . $model->id);
            // updated the deleted_by field of all other records and delete them
            static::query()
                ->where('id', '!=', $model->id)
                ->update(['deleted_by' => $model->created_by, 'deleted_at' => now()]);

            $model->created_by = auth()->id();
            $model->saveQuietly();
        });

        static::updated(function ($model) {
            $model->updated_by = auth()->id();
            $model->saveQuietly();
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->id();
            $model->deleted_at = now();
            $model->save();
        });
    }
}
