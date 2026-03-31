<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * BillingService handles all SaaS billing operations
 */
class BillingService
{
    /**
     * Create an invoice for subscription renewal
     */
    public static function createRenewalInvoice(Subscription $subscription): array
    {
        try {
            $client = $subscription->saasClient;
            $plan = $subscription->plan;
            $amount = $subscription->getAmount();

            $invoice = [
                'saas_client_id'    => $client->id,
                'subscription_id'   => $subscription->id,
                'amount'            => $amount,
                'currency'          => $plan->currency ?? 'KES',
                'status'            => 'pending',
                'due_date'          => now()->addDays(3),
                'invoice_date'      => now(),
                'type'              => 'renewal',
                'metadata'          => [
                    'plan_name'     => $plan->name,
                    'billing_cycle' => $plan->billing_cycle,
                ],
            ];

            Log::info("Renewal invoice created for subscription {$subscription->id}", $invoice);

            return [
                'success' => true,
                'invoice' => $invoice,
                'message' => 'Invoice created successfully',
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to create renewal invoice: {$e->getMessage()}");
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process payment via M-Pesa
     */
    public static function initiateMpesaPayment(
        Subscription $subscription,
        string $phoneNumber
    ): array {
        try {
            $amount = $subscription->getAmount();
            $client = $subscription->saasClient;

            $paymentData = [
                'subscription_id'   => $subscription->id,
                'saas_client_id'    => $client->id,
                'amount'            => $amount,
                'phone'             => $phoneNumber,
                'status'            => 'pending',
                'payment_method'    => 'mpesa',
                'mpesa_ref'         => 'MPESA-' . time(),
            ];

            $payment = SubscriptionPayment::create($paymentData);

            Log::info("M-Pesa payment initiated: {$payment->mpesa_ref}");

            return [
                'success'   => true,
                'payment'   => $payment,
                'reference' => $payment->mpesa_ref,
            ];
        } catch (\Throwable $e) {
            Log::error("M-Pesa payment initiation failed: {$e->getMessage()}");
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle successful payment
     */
    public static function confirmPayment(SubscriptionPayment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            try {
                $payment->update([
                    'status'  => 'successful',
                    'paid_at' => now(),
                ]);

                $subscription = $payment->subscription;

                if ($subscription->isTrialing()) {
                    $subscription->activateFromTrial();
                } elseif ($subscription->isInGracePeriod()) {
                    $subscription->renew();
                } elseif ($subscription->isExpiring()) {
                    $subscription->renew();
                }

                Log::info("Payment {$payment->id} confirmed and processed successfully");
                return true;
            } catch (\Throwable $e) {
                Log::error("Payment confirmation failed: {$e->getMessage()}");
                return false;
            }
        });
    }

    /**
     * Handle failed payment
     */
    public static function handleFailedPayment(SubscriptionPayment $payment, string $reason): bool
    {
        return DB::transaction(function () use ($payment, $reason) {
            try {
                $payment->update([
                    'status'           => 'failed',
                    'failed_at'        => now(),
                    'failure_reason'   => $reason,
                ]);

                $subscription = $payment->subscription;
                
                if ($subscription->isActive() || $subscription->isExpiring()) {
                    $subscription->moveToGracePeriod(7);
                }

                Log::warning("Payment {$payment->id} failed: {$reason}");
                return true;
            } catch (\Throwable $e) {
                Log::error("Failed payment handling failed: {$e->getMessage()}");
                return false;
            }
        });
    }

    /**
     * Check and process subscription renewals
     */
    public static function processRenewals(): array
    {
        $results = [
            'processed'     => 0,
            'failed'        => 0,
            'grace_period'  => 0,
            'expired'       => 0,
        ];

        try {
            $expiringToday = Subscription::where('status', 'active')
                ->whereDate('current_period_end', now()->toDateString())
                ->get();

            foreach ($expiringToday as $subscription) {
                $result = self::createRenewalInvoice($subscription);
                $result['success'] ? $results['processed']++ : $results['failed']++;
            }

            $inGracePeriod = Subscription::where('status', 'grace_period')
                ->where('grace_period_ends_at', '<=', now())
                ->get();

            foreach ($inGracePeriod as $subscription) {
                $subscription->expire();
                $results['expired']++;
            }

            Log::info("Subscription renewal batch processed", $results);
            return $results;
        } catch (\Throwable $e) {
            Log::error("Subscription renewal batch failed: {$e->getMessage()}");
            return $results;
        }
    }

    /**
     * Get subscription usage and calculate overage charges
     */
    public static function calculateOverageCharges(Subscription $subscription): float
    {
        try {
            $plan = $subscription->plan;
            $usage = $subscription->saasClient->usageMetric;

            if (!$usage) {
                return 0;
            }

            $overageProperties = max(0, $usage->properties_used - ($plan->max_properties ?? 0));
            $overagePrice = $plan->overage_price_per_property ?? 0;

            return $overageProperties * $overagePrice;
        } catch (\Throwable $e) {
            Log::error("Overage calculation failed: {$e->getMessage()}");
            return 0;
        }
    }
}