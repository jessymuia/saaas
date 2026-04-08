<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Traits\BelongsToTenant;

class Client extends DefaultAppModel
{
    use BelongsToTenant;

    protected $fillable = [
        'saas_client_id',
        'name',
        'email',
        'phone_number',
        'address',
        'description',
        'status',
        'created_at',
        'created_by',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at'
    ];


    public function manualInvoices()
    {
        return $this->hasMany(ManualInvoices::class);
    }

    protected static function boot(): void
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
}