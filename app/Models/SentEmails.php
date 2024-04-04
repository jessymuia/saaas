<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentEmails extends DefaultAppModel
{
    //
    protected $fillable = [
        'recipient_email',
        'subject',
        'reference_id',
        'body',
        'delivery_status',
        'failure_reason',
        'read_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    public function emailAttachments(){
        return $this->hasMany(EmailAttachments::class,'sent_email_id','id');
    }
}
