<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Provides a track record of the monthly occupation of a unit
 * Act as source of truth for the monthly occupation of a unit to help
 * in the identification of rent/lease without a tenancy bill
 * Class UnitOccupationMonthlyRecords
 */
class UnitOccupationMonthlyRecords extends DefaultAppModel
{
    protected $fillable = [
        'unit_id',
        'tenancy_agreement_id',
        'from_date',
        'end_date',
        'tenancy_bill_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    protected $casts = [
        'from_date' => 'date',
        'end_date' => 'date'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenancyAgreement()
    {
        return $this->belongsTo(TenancyAgreement::class);
    }

    public function tenancyBill()
    {
        return $this->belongsTo(TenancyBill::class);
    }
}
