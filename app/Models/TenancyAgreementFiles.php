<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\TenantScope;
use App\Traits\BelongsToTenant;

class TenancyAgreementFiles extends DefaultAppModel
{
    use BelongsToTenant;
    protected $fillable = [
        'tenancy_agreement_id',
        'name',
        'path',
        'mime_type',
        'extension',
        'size',
        'description',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
        'saas_client_id',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);

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

    public function tenancyAgreement()
    {
        return $this->belongsTo(TenancyAgreement::class);
    }
}
