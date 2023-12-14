<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAttachments extends DefaultAppModel
{
    protected $fillable = [
        'id',
        'sent_email_id',
        'file_name',
        'file_size',
        'mime_type',
        'full_file_path',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    public function sentEmail(){
        return $this->belongsTo(SentEmails::class);
    }
}
