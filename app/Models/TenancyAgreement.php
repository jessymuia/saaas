<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenancyAgreement extends DefaultAppModel
{

    protected $fillable = [
        'unit_id',
        'tenant_id',
        'agreement_type_id',
        'billing_type_id',
        'start_date',
        'end_date',
        'amount',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    // foreign keys
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function agreementType()
    {
        return $this->belongsTo(RefTenancyAgreementType::class);
    }

    public function billingType()
    {
        return $this->belongsTo(RefBillingType::class);
    }
}
