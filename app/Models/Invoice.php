<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends DefaultAppModel
{
    protected $fillable = [
        'tenancy_agreement_id',
        'comments',
        'invoice_status',
        'issue_date',
        'status',
        'archive',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function tenancyAgreement(){
        return $this->hasOne(TenancyAgreement::class,'tenancy_agreement_id','id');
    }
}
