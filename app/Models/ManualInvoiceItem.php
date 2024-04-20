<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualInvoiceItem extends DefaultAppModel
{
    protected $fillable = [
        'manual_invoice_id',
        'name',
        'bill_date',
        'due_date',
        'amount',
        'vat',
        'total_amount',
        'billing_type_id',
    ];

    public function manualInvoice()
    {
        return $this->belongsTo(ManualInvoices::class, 'manual_invoice_id', 'id');
    }

    public function billingType()
    {
        return $this->belongsTo(RefBillingType::class);
    }
}
