<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacationNotices extends DefaultAppModel
{
    protected $fillable = [
        'tenancy_agreement_id',
        'property_id',
        'notice_start_date',
        'notice_end_date',
        'extra_information',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    public function tenancyAgreement()
    {
        return $this->belongsTo(TenancyAgreement::class, 'tenancy_agreement_id', 'id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }
}
