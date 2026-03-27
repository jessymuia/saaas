<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * SubscriptionPayment — every payment made against a subscription.
 * Central / Local table — NOT distributed.
 */
class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $table = 'subscription_payments';

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
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'paid_at'   => 'datetime',
        'failed_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function saasClient()
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
