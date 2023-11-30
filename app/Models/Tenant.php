<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends DefaultAppModel
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

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
}
