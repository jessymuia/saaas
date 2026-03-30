<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'saas_client_id',
        'plan_id',
        'status',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'grace_ends_at',
        'cancelled_at',
        'last_reminded_at',
        'reminder_count',
    ];

    protected $casts = [
        'starts_at'        => 'datetime',
        'ends_at'          => 'datetime',
        'trial_ends_at'    => 'datetime',
        'grace_ends_at'    => 'datetime',
        'cancelled_at'     => 'datetime',
        'last_reminded_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && $this->trial_ends_at?->isFuture();
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }

    public function isInGracePeriod(): bool
    {
        return $this->status === 'grace_period' && $this->grace_ends_at?->isFuture();
    }

    public function isExpired(): bool
    {
        return in_array($this->status, ['expired', 'suspended']);
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function daysUntilTrialEnds(): int
    {
        return (int) now()->diffInDays($this->trial_ends_at, false);
    }

    public function daysUntilGraceEnds(): int
    {
        return (int) now()->diffInDays($this->grace_ends_at, false);
    }

    public static function startTrial(string $saasClientId, int $planId): self
    {
        return self::withoutEvents(function () use ($saasClientId, $planId) {
            return self::create([
                'saas_client_id' => $saasClientId,
                'plan_id'        => $planId,
                'status'         => 'trialing',
                'billing_cycle'  => 'monthly',
                'starts_at'      => now(),
                'ends_at'        => now()->addDays(60),
                'trial_ends_at'  => now()->addDays(60),
            ]);
        });
    }

    public function startGracePeriod(): void
    {
        $this->update([
            'status'        => 'grace_period',
            'grace_ends_at' => now()->addDays(3),
        ]);
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
        $this->client->update([
            'is_suspended'      => true,
            'suspended_at'      => now(),
            'suspension_reason' => 'Subscription expired',
        ]);
    }

    public function renew(string $billingCycle = 'monthly'): void
    {
        $days = $billingCycle === 'yearly' ? 365 : 30;
        $this->update([
            'status'        => 'active',
            'billing_cycle' => $billingCycle,
            'starts_at'     => now(),
            'ends_at'       => now()->addDays($days),
            'grace_ends_at' => null,
            'cancelled_at'  => null,
        ]);
        $this->client->update([
            'is_suspended'      => false,
            'suspended_at'      => null,
            'suspension_reason' => null,
        ]);
    }
}