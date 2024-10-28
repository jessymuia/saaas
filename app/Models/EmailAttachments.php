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
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
        'status',
        'archive'
    ];

    public function sentEmail(){
        return $this->belongsTo(SentEmails::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->created_by = auth()->id();
            $model->saveQuietly();
        });

        static::updated(function ($model) {
            $model->updated_by = auth()->id();
            $model->saveQuietly();
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->id();
            $model->deleted_at = now();
            $model->save();
        });
    }
}
