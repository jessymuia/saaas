<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyUtility extends DefaultAppModel
{
    protected $fillable = [
        'property_id',
        'utility_id',
        'rate_per_unit',
        'billing_type_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    // foreign keys
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function utility()
    {
        return $this->belongsTo(RefUtility::class);
    }

    public function billingType()
    {
        return $this->belongsTo(RefBillingType::class, 'billing_type_id');
    }
}
