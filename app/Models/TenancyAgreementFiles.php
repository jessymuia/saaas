<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenancyAgreementFiles extends DefaultAppModel
{
    protected $fillable = [
        'tenancy_agreement_id',
        'name',
        'path',
        'mime_type',
        'extension',
        'size',
        'description',
    ];

    public function tenancyAgreement()
    {
        return $this->belongsTo(TenancyAgreement::class);
    }
}
