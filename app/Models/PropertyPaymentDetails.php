<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyPaymentDetails extends DefaultAppModel
{
    protected $fillable = [
        'property_id',
        'account_name',
        'account_number',
        'bank_name',
        'mpesa_paybill_number',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
