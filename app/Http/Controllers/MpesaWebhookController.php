<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPayment;
use App\Services\BillingService;
use App\Services\MpesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * MpesaWebhookController
 *
 * Receives Safaricom Daraja API callback POSTs.
 *
 * IMPORTANT: This endpoint must be publicly accessible (no CSRF / auth middleware).
 * Register it in routes/api.php under the 'mpesa' prefix.
 *
 * Safaricom will POST to:
 *   POST /api/mpesa/callback
 */
class MpesaWebhookController extends Controller
{
    /**
     * Handle the incoming M-Pesa callback.
     *
     * Safaricom expects a 200 response with a specific JSON body.
     * Any other response will cause retries.
     */
    public function callback(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('M-Pesa callback received', [
            'ip'      => $request->ip(),
            'payload' => $payload,
        ]);

        try {
            $mpesaService = new MpesaService();
            $parsed = $mpesaService->parseCallback($payload);

            if (!$parsed['success']) {
                Log::warning('M-Pesa callback indicates payment failure', $parsed);
                // Still return 200 — Safaricom considers non-200 as a failed delivery
                return $this->safaricomSuccess('Payment failed or cancelled');
            }

            // Find the pending payment by checkout request ID
            $checkoutRequestId = $parsed['checkout_request_id'] ?? null;

            if ($checkoutRequestId) {
                $payment = SubscriptionPayment::where('mpesa_ref', $checkoutRequestId)
                    ->orWhere('mpesa_ref', 'LIKE', '%' . $checkoutRequestId . '%')
                    ->first();

                if ($payment) {
                    // Update the payment with M-Pesa receipt details
                    // Only update columns that exist in the subscription_payments schema
                    $updateData = ['mpesa_ref' => $parsed['mpesa_receipt'] ?? $payment->mpesa_ref];
                    if (!empty($parsed['amount'])) {
                        $updateData['amount'] = $parsed['amount'];
                    }
                    $payment->update($updateData);

                    BillingService::confirmPayment($payment);

                    Log::info('M-Pesa payment confirmed', [
                        'payment_id'    => $payment->id,
                        'receipt'       => $parsed['mpesa_receipt'],
                        'amount'        => $parsed['amount'],
                    ]);
                } else {
                    Log::warning('M-Pesa callback: no matching payment found for checkout ID', [
                        'checkout_request_id' => $checkoutRequestId,
                    ]);
                }
            }

            return $this->safaricomSuccess('Callback processed');
        } catch (\Throwable $e) {
            Log::error('M-Pesa callback processing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Still return 200 to prevent Safaricom retries for server errors
            return $this->safaricomSuccess('Internal error — logged');
        }
    }

    /**
     * Return the standard Safaricom success acknowledgement response.
     */
    private function safaricomSuccess(string $description = 'Success'): JsonResponse
    {
        return response()->json([
            'ResultCode'        => 0,
            'ResultDesc'        => $description,
        ]);
    }
}
