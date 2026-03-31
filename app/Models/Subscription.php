<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Subscription — tracks all SaaS client subscriptions
 * Central / Local table — NOT distributed
 */
class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'saas_client_id',
        'plan_id',
        'status',
        'current_period_start',
        'current_period_end',
        'trial_ends_at',
        'grace_period_ends_at',
        'cancellation_date',
        'cancellation_reason',
        'renewal_attempts',
        'metadata',
    ];

    protected $casts = [
        'current_period_start'  => 'datetime',
        'current_period_end'    => 'datetime',
        'trial_ends_at'         => 'datetime',
        'grace_period_ends_at'  => 'datetime',
        'cancellation_date'     => 'datetime',
        'renewal_attempts'      => 'integer',
        'metadata'              => 'array',
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

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class, 'subscription_id');
    }

    // ────────────────────────────────────────────────────────────
    // STATIC FACTORY METHODS
    // ────────────────────────────────────────────────────────────

    /**
     * Create a new trial subscription
     */
    public static function startTrial(string $saasClientId, int $planId): self
    {
        $plan = Plan::findOrFail($planId);
        
        $trialDays = $plan->trial_days ?? 14;
        $trialEndsAt = now()->addDays($trialDays);

        return self::create([
            'saas_client_id'        => $saasClientId,
            'plan_id'               => $planId,
            'status'                => 'trialing',
            'current_period_start'  => now(),
            'current_period_end'    => $trialEndsAt,
            'trial_ends_at'         => $trialEndsAt,
            'renewal_attempts'      => 0,
            'metadata'              => [
                'trial_started_at' => now()->toIso8601String(),
                'source' => 'new_signup',
            ],
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
        return $this->status === 'active' && $this->current_period_end?->isFuture();
    }

    public function isInGracePeriod(): bool
    {
        return $this->status === 'grace_period' && $this->grace_period_ends_at?->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->status === 'active' && $this->current_period_end?->isPast());
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
               $this->current_period_end?->lessThanOrEqualTo(now()->addDays(7));
    }

    // ────────────────────────────────────────────────────────────
    // LIFECYCLE METHODS
    // ────────────────────────────────────────────────────────────

    /**
     * Activate subscription after trial period ends
     */
    public function activateFromTrial(): void
    {
        $this->update([
            'status'                => 'active',
            'current_period_start'  => now(),
            'current_period_end'    => now()->addMonth(),
            'trial_ends_at'         => null,
        ]);

        \Illuminate\Support\Facades\Log::info(
            "Subscription {$this->id} activated from trial for client {$this->saas_client_id}"
        );
    }

    /**
     * Renew subscription for another period
     */
    public function renew(): bool
    {
        try {
            $plan = $this->plan;
            
            $billingCycle = $plan->billing_cycle ?? 'monthly';
            $currentPeriodEnd = match($billingCycle) {
                'annual'    => $this->current_period_end->addYear(),
                'quarterly' => $this->current_period_end->addQuarter(),
                default     => $this->current_period_end->addMonth(),
            };

            $this->update([
                'status'                => 'active',
                'current_period_start'  => $this->current_period_end,
                'current_period_end'    => $currentPeriodEnd,
                'renewal_attempts'      => 0,
                'grace_period_ends_at'  => null,
            ]);

            \Illuminate\Support\Facades\Log::info(
                "Subscription {$this->id} renewed until {$currentPeriodEnd}"
            );

            return true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                "Subscription renewal failed: {$e->getMessage()}"
            );
            return false;
        }
    }

    /**
     * Move to grace period after payment failure
     */
    public function moveToGracePeriod(int $graceDays = 7): void
    {
        $this->update([
            'status'                => 'grace_period',
            'grace_period_ends_at'  => now()->addDays($graceDays),
            'renewal_attempts'      => $this->renewal_attempts + 1,
        ]);

        \Illuminate\Support\Facades\Log::warning(
            "Subscription {$this->id} moved to grace period. Grace ends: " . 
            $this->grace_period_ends_at->toDateString()
        );
    }

    /**
     * Expire subscription (payment failed in grace period)
     */
    public function expire(): void
    {
        $this->update([
            'status'                => 'expired',
            'grace_period_ends_at'  => null,
        ]);

        $this->saasClient->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => 'Payment failed - subscription expired',
        ]);

        \Illuminate\Support\Facades\Log::error(
            "Subscription {$this->id} expired. SaaS client {$this->saas_client_id} suspended."
        );
    }

    /**
     * Cancel subscription
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status'                => 'canceled',
            'cancellation_date'     => now(),
            'cancellation_reason'   => $reason,
            'grace_period_ends_at'  => null,
        ]);

        \Illuminate\Support\Facades\Log::info(
            "Subscription {$this->id} canceled. Reason: {$reason}"
        );
    }

    /**
     * Suspend subscription
     */
    public function suspend(string $reason = null): void
    {
        $this->update(['status' => 'suspended']);

        $this->saasClient->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => $reason,
        ]);

        \Illuminate\Support\Facades\Log::warning(
            "Subscription {$this->id} suspended. Reason: {$reason}"
        );
    }

    /**
     * Reactivate suspended subscription
     */
    public function reactivate(): void
    {
        $this->update(['status' => 'active']);
        
        $this->saasClient->update([
            'is_suspended' => false,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        \Illuminate\Support\Facades\Log::info(
            "Subscription {$this->id} reactivated. SaaS client {$this->saas_client_id} unsuspended."
        );
    }

    // ────────────────────────────────────────────────────────────
    // BILLING CALCULATIONS
    // ────────────────────────────────────────────────────────────

    /**
     * Get subscription amount for current period
     */
    public function getAmount(): float
    {
        return (float) $this->plan->price;
    }

    /**
     * Calculate prorated amount for partial periods
     */
    public function calculateProration(): float
    {
        $planPrice = $this->getAmount();
        
        $totalDays = $this->current_period_start->diffInDays($this->current_period_end);
        $remainingDays = now()->diffInDays($this->current_period_end);

        if ($totalDays === 0) {
            return $planPrice;
        }

        return round(($planPrice / $totalDays) * $remainingDays, 2);
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
        return $query
            ->where('status', 'active')
            ->whereBetween('current_period_end', [
                now(),
                now()->addDays(7),
            ]);
    }

    public function scopeExpired($query)
    {
        return $query
            ->where(function ($q) {
                $q->where('status', 'expired')
                  ->orWhere(function ($subQ) {
                      $subQ->where('status', 'active')
                           ->where('current_period_end', '<', now());
                  });
            });
    }

    public function scopeGracePeriod($query)
    {
        return $query->where('status', 'grace_period');
    }
}