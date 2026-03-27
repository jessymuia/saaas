<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\TenantScope;
use App\Traits\BelongsToTenant;

class VacationNotices extends DefaultAppModel
{
    use BelongsToTenant;
    protected $fillable = [
        'tenancy_agreement_id',
        'property_id',
        'notice_start_date',
        'notice_end_date',
        'extra_information',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
        'status',
        'archive',
        'saas_client_id',
    ];

    protected static function boot()
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
        return $this->belongsTo(TenancyAgreement::class, 'tenancy_agreement_id', 'id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }

    public function scopeAccessibleByUser(Builder $query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        return $query->whereHas('property', function (Builder $query) use ($user) {
            $query->whereHas('users', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('property_management_users.status', true);
            });
        });
    }
}
