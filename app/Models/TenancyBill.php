<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\TenantScope;
use App\Traits\BelongsToTenant;

class TenancyBill extends DefaultAppModel
{
    use BelongsToTenant;
    protected $fillable = [
        'tenancy_agreement_id',
        'name',
        'bill_date',
        'due_date',
        'amount',
        'vat',
        'total_amount',
        'billing_type_id',
        'service_id',
        'utility_id',
        'is_deposit',
        'invoice_id',
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

    public function tenancyAgreement()
    {
        return $this->belongsTo(TenancyAgreement::class);
    }

    public function billingType()
    {
        return $this->belongsTo(RefBillingType::class, 'billing_type_id');
    }

    public function service()
    {
        return $this->belongsTo(Services::class);
    }

    public function utility()
    {
        return $this->belongsTo(RefUtility::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
