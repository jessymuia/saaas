<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'saas_client_id',
        'subject',
        'message',
        'status',
        'priority',
    ];

    public function saasClient(): BelongsTo
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
    }
}
