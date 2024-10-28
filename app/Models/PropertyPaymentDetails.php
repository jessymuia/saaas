<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyPaymentDetails extends DefaultAppModel
{
    protected $fillable = [
        'property_id',
        'account_name',
        'account_number',
        'bank_name',
        'mpesa_paybill_number',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
        'status',
        'archive'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
