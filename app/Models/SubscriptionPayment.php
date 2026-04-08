<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPayment extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'subscription_payments';

    protected $fillable = [
        'subscription_id',
        'saas_client_id',
        'amount',
        'currency',
        'payment_method',
        'mpesa_ref',
        'status',
        'paid_at',
        'failed_at',
        'failure_reason',
        'version',
        'archive',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'paid_at'    => 'datetime',
        'failed_at'  => 'datetime',
        'archive'    => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function saasClient(): BelongsTo
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function isSuccessful(): bool { return $this->status === 'successful'; }
    public function isPending(): bool    { return $this->status === 'pending'; }
    public function isFailed(): bool     { return $this->status === 'failed'; }

    public function scopeSuccessful($query) { return $query->where('status', 'successful'); }
    public function scopePending($query)    { return $query->where('status', 'pending'); }
}
