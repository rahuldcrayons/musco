<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $mode = config('services.paypal.mode', 'live');
        $this->baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Get OAuth 2.0 access token (cached for 8 hours).
     */
    public function getAccessToken(): string
    {
        return Cache::remember('paypal_access_token', 28800, function () {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post($this->baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if (!$response->successful()) {
                Log::error('PayPal: Failed to get access token', ['response' => $response->json()]);
                throw new \RuntimeException('Failed to authenticate with PayPal.');
            }

            return $response->json('access_token');
        });
    }

    /**
     * Create a PayPal order.
     */
    public function createOrder(float $amount, string $currency, string $referenceId, string $description = ''): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->timeout(15)
            ->post($this->baseUrl . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $referenceId,
                    'description' => $description ?: ('Order from ' . config('app.name')),
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                ]],
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                            'user_action' => 'PAY_NOW',
                            'return_url' => route('checkout.paypal.return'),
                            'cancel_url' => route('checkout.paypal.cancel'),
                        ],
                    ],
                ],
            ]);

        if (!$response->successful()) {
            Log::error('PayPal: Failed to create order', ['response' => $response->json()]);
            throw new \RuntimeException('PayPal order creation failed: ' . ($response->json('message') ?? 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Capture a PayPal order after buyer approval.
     */
    public function captureOrder(string $orderId): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->timeout(15)
            ->withHeaders(['Prefer' => 'return=representation'])
            ->post($this->baseUrl . '/v2/checkout/orders/' . $orderId . '/capture', []);

        if (!$response->successful()) {
            Log::error('PayPal: Failed to capture order', [
                'order_id' => $orderId,
                'response' => $response->json(),
            ]);
            throw new \RuntimeException('PayPal capture failed: ' . ($response->json('message') ?? 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Get order details.
     */
    public function getOrder(string $orderId): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->timeout(10)
            ->get($this->baseUrl . '/v2/checkout/orders/' . $orderId);

        if (!$response->successful()) {
            throw new \RuntimeException('Failed to fetch PayPal order.');
        }

        return $response->json();
    }

    /**
     * Check if PayPal is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Get the JS SDK client ID.
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }
}
