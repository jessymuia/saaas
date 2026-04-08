<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * MpesaService — Safaricom Daraja API Integration Scaffolding
 *
 * Covers:
 *  - OAuth token acquisition (cached)
 *  - STK Push (Lipa Na M-Pesa Online — customer-initiated payment prompt)
 *  - STK Push Query (check payment status)
 *  - C2B webhook payload parsing
 *
 * IMPORTANT:
 *  - No credentials are stored here. All config values come from config/services.php
 *    which reads from .env (never commit credentials).
 *  - Sandbox vs. production is controlled by MPESA_ENV=sandbox|production in .env.
 *
 * Usage:
 *   $service = new MpesaService();
 *   $result  = $service->stkPush('2547XXXXXXXX', 1000, 'INVOICE-001', $callbackUrl);
 */
class MpesaService
{
    private string $baseUrl;
    private string $consumerKey;
    private string $consumerSecret;
    private string $shortcode;
    private string $passkey;
    private string $callbackUrl;

    public function __construct()
    {
        $env = config('mpesa.env', 'sandbox');

        $this->baseUrl = config("mpesa.base_url.{$env}", 'https://sandbox.safaricom.co.ke');

        $this->consumerKey    = config('mpesa.consumer_key', '');
        $this->consumerSecret = config('mpesa.consumer_secret', '');
        $this->shortcode      = config('mpesa.shortcode', '');
        $this->passkey        = config('mpesa.passkey', '');
        $this->callbackUrl    = config('mpesa.callback_url', '');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 1. OAuth Token
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Get a valid OAuth access token, cached for its lifetime.
     */
    public function getAccessToken(): string
    {
        return Cache::remember('mpesa_access_token', config('mpesa.token_cache_seconds', 3500), function () {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");

            if (!$response->successful()) {
                Log::error('M-Pesa token request failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \RuntimeException('Failed to obtain M-Pesa access token.');
            }

            return $response->json('access_token');
        });
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 2. STK Push (Lipa Na M-Pesa Online)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Initiate an STK Push payment prompt to the customer's phone.
     *
     * @param  string  $phone       Phone in format 2547XXXXXXXX (international, no +)
     * @param  float   $amount      Amount in KES (min 1)
     * @param  string  $reference   Bill reference (e.g., invoice number) — max 12 chars
     * @param  string  $description Transaction description — max 13 chars
     * @return array   Response from Daraja API or error array
     */
    public function stkPush(
        string $phone,
        float $amount,
        string $reference = 'Payment',
        string $description = 'SaaS Payment'
    ): array {
        try {
            $timestamp = now()->format('YmdHis');
            $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => (int) ceil($amount),
                'PartyA'            => $phone,
                'PartyB'            => $this->shortcode,
                'PhoneNumber'       => $phone,
                'CallBackURL'       => $this->callbackUrl,
                'AccountReference'  => substr($reference, 0, 12),
                'TransactionDesc'   => substr($description, 0, 13),
            ];

            $response = Http::withToken($this->getAccessToken())
                ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", $payload);

            $body = $response->json();

            if (!$response->successful() || ($body['ResponseCode'] ?? '1') !== '0') {
                Log::error('M-Pesa STK Push failed', [
                    'phone'    => $phone,
                    'amount'   => $amount,
                    'response' => $body,
                ]);
                return ['success' => false, 'error' => $body['errorMessage'] ?? 'STK Push failed', 'response' => $body];
            }

            Log::info('M-Pesa STK Push initiated', [
                'phone'         => $phone,
                'amount'        => $amount,
                'checkout_id'   => $body['CheckoutRequestID'] ?? null,
            ]);

            return [
                'success'             => true,
                'checkout_request_id' => $body['CheckoutRequestID'] ?? null,
                'merchant_request_id' => $body['MerchantRequestID'] ?? null,
                'response_code'       => $body['ResponseCode'] ?? null,
                'response_description' => $body['ResponseDescription'] ?? null,
                'customer_message'    => $body['CustomerMessage'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('M-Pesa STK Push exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 3. STK Push Query (check status of a pending STK Push)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Query the status of a previous STK Push.
     *
     * @param  string  $checkoutRequestId  The CheckoutRequestID from stkPush()
     * @return array
     */
    public function stkPushQuery(string $checkoutRequestId): array
    {
        try {
            $timestamp = now()->format('YmdHis');
            $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $response = Http::withToken($this->getAccessToken())
                ->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", [
                    'BusinessShortCode' => $this->shortcode,
                    'Password'          => $password,
                    'Timestamp'         => $timestamp,
                    'CheckoutRequestID' => $checkoutRequestId,
                ]);

            $body = $response->json();

            return [
                'success'      => $response->successful(),
                'result_code'  => $body['ResultCode'] ?? null,
                'result_desc'  => $body['ResultDesc'] ?? null,
                'response'     => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('M-Pesa STK Query exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 4. Parse C2B Webhook Callback
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Parse and validate an incoming M-Pesa webhook callback payload.
     *
     * @param  array  $payload  Raw JSON payload from the webhook
     * @return array  Normalised payment details or error info
     */
    public function parseCallback(array $payload): array
    {
        try {
            $body = $payload['Body']['stkCallback'] ?? null;

            if ($body === null) {
                return ['success' => false, 'error' => 'Invalid callback payload structure'];
            }

            $resultCode = (int) ($body['ResultCode'] ?? -1);

            if ($resultCode !== 0) {
                return [
                    'success'     => false,
                    'result_code' => $resultCode,
                    'result_desc' => $body['ResultDesc'] ?? 'Payment failed or cancelled',
                    'checkout_request_id' => $body['CheckoutRequestID'] ?? null,
                ];
            }

            // Extract metadata items from the callback
            $items = collect($body['CallbackMetadata']['Item'] ?? [])
                ->keyBy('Name')
                ->map(fn($item) => $item['Value'] ?? null);

            return [
                'success'             => true,
                'result_code'         => $resultCode,
                'amount'              => $items->get('Amount'),
                'mpesa_receipt'       => $items->get('MpesaReceiptNumber'),
                'transaction_date'    => $items->get('TransactionDate'),
                'phone'               => $items->get('PhoneNumber'),
                'checkout_request_id' => $body['CheckoutRequestID'] ?? null,
                'merchant_request_id' => $body['MerchantRequestID'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('M-Pesa callback parse error: ' . $e->getMessage(), $payload);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
