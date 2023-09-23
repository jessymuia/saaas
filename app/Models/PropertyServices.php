<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyServices extends DefaultAppModel
{
    protected $fillable = [
        'property_id',
        'service_id',
        'rate',
        'billing_type_id',
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

    public function service()
    {
        return $this->belongsTo(Services::class);
    }

    public function billingType()
    {
        return $this->belongsTo(RefBillingType::class, 'billing_type_id');
    }
}
