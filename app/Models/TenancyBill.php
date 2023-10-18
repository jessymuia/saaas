<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenancyBill extends DefaultAppModel
{
    protected $fillable = [
        'tenancy_agreement_id',
        'name',
        'bill_date',
        'due_date',
        'amount',
        'billing_type_id',
        'service_id',
        'utility_id',
        'invoice_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

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
}
