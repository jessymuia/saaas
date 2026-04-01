<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Scopes\TenantScope;
use App\Traits\BelongsToTenant;

class Tenant extends DefaultAppModel
{
    use BelongsToTenant;
    protected $fillable = [
        'name',
        'email',
        'phone_number',
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

    public function tenancyAgreements()
    {
        return $this->hasMany(TenancyAgreement::class);
    }

    public function tenancyBills()
    {
        return $this->hasManyThrough(
            TenancyBill::class,
            TenancyAgreement::class,
            'tenant_id',
            'tenancy_agreement_id',
            'id',
            'id');
    }
    public function getTenancyStatusAttribute(): string
    {
        $latestAgreement = $this->tenancyAgreements()
            ->latest('start_date')
            ->first();

        if (!$latestAgreement) {
            return 'Inactive';
        }


        if (!$latestAgreement->end_date || Carbon::parse($latestAgreement->end_date)->isFuture()) {
            return 'Active';
        }


        return 'Inactive';
    }


    // get invoices belonging to given tenant
    public function invoices()
    {
        return $this->hasManyThrough(
            Invoice::class,
            TenancyAgreement::class,
            'tenant_id',
            'tenancy_agreement_id',
            'id',
            'id');
    }

    // get payments belonging to given tenant
    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class, 'tenant_id', 'id');
    }

    public function scopeAccessibleByUser(Builder $query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        return $query->whereHas('tenancyAgreements.unit.property', function (Builder $query) use ($user) {
            $query->whereHas('users', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('property_management_users.status', true);
            });
        });
    }
}
