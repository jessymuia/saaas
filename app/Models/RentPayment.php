<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentPayment extends DefaultAppModel
{
    protected $fillable = [
        'tenancy_agreement_id',
        'payment_type_id',
        'received_by',
        'payment_date',
        'amount',
        'paid_by',
        'payment_reference',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    // foreign keys
    public function tenancyAgreement()
    {
        return $this->belongsTo(TenancyAgreement::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(RefPaymentType::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
