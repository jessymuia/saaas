<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscalationRatesAndAmountsLogs extends DefaultAppModel
{
    protected $fillable = [
        'tenancy_agreement_id',
        'escalation_rate',
        'previous_amount',
        'new_amount',
        'escalation_date',
        'status',
        'archive',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function tenancyAgreement()
    {
        return $this->belongsTo(TenancyAgreement::class);
    }
}
