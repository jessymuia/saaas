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
        'document_generated_at',
        'document_sent_at',
        'document_generated_by',
        'is_confirmed',
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

    public function documentGeneratedBy()
    {
        return $this->belongsTo(User::class, 'document_generated_by');
    }
}
