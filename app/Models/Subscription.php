<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Subscription — tracks all SaaS client subscriptions
 *
 * Actual DB columns: id, saas_client_id, plan_id, status, billing_cycle,
 * starts_at, ends_at, trial_ends_at, cancelled_at, grace_ends_at,
 * last_reminded_at, reminder_count, created_at, updated_at
 */
class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'saas_client_id',
        'plan_id',
        'status',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'grace_ends_at',
        'last_reminded_at',
        'reminder_count',
    ];

    protected $casts = [
        'starts_at'        => 'datetime',
        'ends_at'          => 'datetime',
        'trial_ends_at'    => 'datetime',
        'cancelled_at'     => 'datetime',
        'grace_ends_at'    => 'datetime',
        'last_reminded_at' => 'datetime',
        'reminder_count'   => 'integer',
    ];

    // ────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ────────────────────────────────────────────────────────────

    public function saasClient(): BelongsTo
    {
        return $this->belongsTo(SaasClient::class, 'saas_client_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    // ────────────────────────────────────────────────────────────
    // STATIC FACTORY METHODS
    // ────────────────────────────────────────────────────────────

    public static function startTrial(string $saasClientId, int $planId): self
    {
        $plan = Plan::findOrFail($planId);

        $trialDays   = 14;
        $trialEndsAt = now()->addDays($trialDays);

        return self::create([
            'saas_client_id' => $saasClientId,
            'plan_id'        => $planId,
            'status'         => 'trialing',
            'billing_cycle'  => 'monthly',
            'starts_at'      => now(),
            'ends_at'        => $trialEndsAt,
            'trial_ends_at'  => $trialEndsAt,
            'reminder_count' => 0,
        ]);
    }

    // ────────────────────────────────────────────────────────────
    // STATUS CHECKS
    // ────────────────────────────────────────────────────────────

    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && $this->trial_ends_at?->isFuture();
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at?->isFuture();
    }

    public function isInGracePeriod(): bool
    {
        return $this->status === 'grace_period' && $this->grace_ends_at?->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
               ($this->status === 'active' && $this->ends_at?->isPast());
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isExpiring(): bool
    {
        return $this->status === 'active' &&
               $this->ends_at?->lessThanOrEqualTo(now()->addDays(7));
    }

    // ────────────────────────────────────────────────────────────
    // LIFECYCLE METHODS
    // ────────────────────────────────────────────────────────────

    public function activateFromTrial(): void
    {
        $this->update([
            'status'         => 'active',
            'starts_at'      => now(),
            'ends_at'        => now()->addMonth(),
            'trial_ends_at'  => null,
        ]);
    }

    public function renew(): bool
    {
        try {
            $newEnd = match ($this->billing_cycle) {
                'annual'    => $this->ends_at->addYear(),
                'quarterly' => $this->ends_at->addQuarter(),
                default     => $this->ends_at->addMonth(),
            };

            $this->update([
                'status'         => 'active',
                'starts_at'      => $this->ends_at,
                'ends_at'        => $newEnd,
                'reminder_count' => 0,
                'grace_ends_at'  => null,
            ]);

            return true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Subscription renewal failed: {$e->getMessage()}");
            return false;
        }
    }

    public function moveToGracePeriod(int $graceDays = 7): void
    {
        $this->update([
            'status'         => 'grace_period',
            'grace_ends_at'  => now()->addDays($graceDays),
            'reminder_count' => $this->reminder_count + 1,
        ]);
    }

    public function expire(): void
    {
        $this->update([
            'status'        => 'expired',
            'grace_ends_at' => null,
        ]);

        $this->saasClient->update([
            'is_suspended'      => true,
            'suspended_at'      => now(),
            'suspension_reason' => 'Payment failed — subscription expired',
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status'       => 'canceled',
            'cancelled_at' => now(),
            'grace_ends_at' => null,
        ]);
    }

    public function suspend(string $reason = null): void
    {
        $this->update(['status' => 'suspended']);

        $this->saasClient->update([
            'is_suspended'      => true,
            'suspended_at'      => now(),
            'suspension_reason' => $reason,
        ]);
    }

    public function reactivate(): void
    {
        $this->update(['status' => 'active']);

        $this->saasClient->update([
            'is_suspended'      => false,
            'suspended_at'      => null,
            'suspension_reason' => null,
        ]);
    }

    // ────────────────────────────────────────────────────────────
    // SCOPES
    // ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrialing($query)
    {
        return $query->where('status', 'trialing');
    }

    public function scopeExpiring($query)
    {
        return $query->where('status', 'active')
                     ->whereBetween('ends_at', [now(), now()->addDays(7)]);
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'active')->where('ends_at', '<', now());
              });
        });
    }

    public function scopeGracePeriod($query)
    {
        return $query->where('status', 'grace_period');
    }
}
