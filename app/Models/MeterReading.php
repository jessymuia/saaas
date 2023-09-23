<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterReading extends DefaultAppModel
{
    protected $fillable = [
        'tenancy_agreement_id',
        'utility_id',
        'reading_date',
        'current_reading',
        'previous_reading',
        'consumption',
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

    public function utility()
    {
        return $this->belongsTo(RefUtility::class);
    }
}
