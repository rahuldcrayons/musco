<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class StripeService
{
    private string $secretKey;
    private string $publishableKey;
    private string $webhookSecret;
    private string $apiBase = 'https://api.stripe.com/v1';

    public function __construct()
    {
        $this->secretKey = Setting::get('stripe_secret_key', config('services.stripe.secret', ''));
        $this->publishableKey = Setting::get('stripe_publishable_key', config('services.stripe.key', ''));
        $this->webhookSecret = Setting::get('stripe_webhook_secret', config('services.stripe.webhook_secret', ''));
    }

    public function isConfigured(): bool
    {
        return !empty($this->secretKey) && !empty($this->publishableKey);
    }

    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }

    public function getWebhookSecret(): string
    {
        return $this->webhookSecret;
    }

    /**
     * Create a Stripe Checkout Session (redirect-based flow).
     */
    public function createCheckoutSession(
        float  $amount,
        string $currency,
        string $successUrl,
        string $cancelUrl,
        string $reference,
        string $description = '',
        ?string $customerEmail = null
    ): array {
        $params = [
            'mode'                 => 'payment',
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => strtolower($currency),
                    'unit_amount'  => (int) round($amount * 100), // Stripe uses smallest currency unit (pence)
                    'product_data' => [
                        'name'        => $description ?: 'Order from ' . config('app.name'),
                        'description' => $reference,
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url'          => $successUrl,
            'cancel_url'           => $cancelUrl,
            'client_reference_id'  => $reference,
            'metadata'             => ['reference' => $reference],
        ];

        if ($customerEmail) {
            $params['customer_email'] = $customerEmail;
        }

        return $this->request('POST', '/checkout/sessions', $params);
    }

    /**
     * Retrieve a Checkout Session by ID.
     */
    public function retrieveSession(string $sessionId): array
    {
        return $this->request('GET', "/checkout/sessions/{$sessionId}");
    }

    /**
     * Retrieve a Payment Intent by ID.
     */
    public function retrievePaymentIntent(string $paymentIntentId): array
    {
        return $this->request('GET', "/payment_intents/{$paymentIntentId}");
    }

    /**
     * Create a refund.
     */
    public function createRefund(string $paymentIntentId, ?float $amount = null): array
    {
        $params = ['payment_intent' => $paymentIntentId];
        if ($amount !== null) {
            $params['amount'] = (int) round($amount * 100);
        }
        return $this->request('POST', '/refunds', $params);
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $sigHeader): bool
    {
        if (empty($this->webhookSecret)) {
            Log::channel('stripe')->warning('Webhook secret not configured, skipping verification');
            return true;
        }

        $elements = explode(',', $sigHeader);
        $timestamp = null;
        $signatures = [];

        foreach ($elements as $element) {
            $parts = explode('=', $element, 2);
            if (count($parts) === 2) {
                if ($parts[0] === 't') {
                    $timestamp = $parts[1];
                } elseif ($parts[0] === 'v1') {
                    $signatures[] = $parts[1];
                }
            }
        }

        if (!$timestamp || empty($signatures)) {
            return false;
        }

        // Reject if timestamp is older than 5 minutes
        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $signedPayload = "{$timestamp}.{$payload}";
        $expectedSignature = hash_hmac('sha256', $signedPayload, $this->webhookSecret);

        foreach ($signatures as $sig) {
            if (hash_equals($expectedSignature, $sig)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Make a request to the Stripe API (no SDK required).
     */
    private function request(string $method, string $endpoint, array $params = []): array
    {
        $url = $this->apiBase . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
            'Stripe-Version: 2024-04-10',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->flatten($params)));
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_setopt($ch, CURLOPT_CAINFO, base_path('cacert.pem'));
        curl_close($ch);

        if ($error) {
            Log::channel('stripe')->error("Stripe cURL error: {$error}");
            throw new \RuntimeException("Stripe API request failed: {$error}");
        }

        $decoded = json_decode($response, true) ?? [];

        if ($httpCode >= 400) {
            $msg = $decoded['error']['message'] ?? "HTTP {$httpCode}";
            Log::channel('stripe')->error("Stripe API error: {$msg}", [
                'endpoint' => $endpoint,
                'status' => $httpCode,
                'response' => $decoded,
            ]);
            throw new \RuntimeException("Stripe API error: {$msg}");
        }

        return $decoded;
    }

    /**
     * Flatten nested arrays for Stripe's form-encoded API format.
     * e.g. ['line_items' => [['price_data' => ['currency' => 'gbp']]]]
     * becomes ['line_items[0][price_data][currency]' => 'gbp']
     */
    private function flatten(array $data, string $prefix = ''): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $newKey = $prefix === '' ? $key : "{$prefix}[{$key}]";
            if (is_array($value)) {
                $result = array_merge($result, $this->flatten($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }
}
