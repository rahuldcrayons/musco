<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnalyticsService
{
    /**
     * Generate a unique event ID for deduplication between client-side pixel and server-side CAPI.
     */
    public static function generateEventId(string $prefix = 'evt'): string
    {
        return $prefix . '_' . Str::uuid()->toString();
    }

    // ─── Purchase ────────────────────────────────────────────────────────

    public function trackPurchase(Order $order, ?Request $request = null, ?string $eventId = null): void
    {
        $this->sendGA4PurchaseEvent($order);
        $this->sendFBPurchaseEvent($order, $request, $eventId);
    }

    // ─── ViewContent ─────────────────────────────────────────────────────

    public function trackViewContent(Product $product, ?Request $request = null, ?string $eventId = null): void
    {
        $this->sendFBCAPIEvent('ViewContent', [
            'content_ids' => [(string) $product->id],
            'content_name' => $product->name,
            'content_category' => $product->category?->name ?? '',
            'content_type' => 'product',
            'value' => (float) $product->price,
            'currency' => 'INR',
        ], $request, auth()->user(), $eventId);
    }

    // ─── AddToCart ────────────────────────────────────────────────────────

    public function trackAddToCart(Product $product, int $quantity, ?Request $request = null, ?string $eventId = null): void
    {
        $this->sendFBCAPIEvent('AddToCart', [
            'content_ids' => [(string) $product->id],
            'content_name' => $product->name,
            'content_type' => 'product',
            'value' => (float) $product->price * $quantity,
            'currency' => 'INR',
            'num_items' => $quantity,
        ], $request, auth()->user(), $eventId);
    }

    // ─── InitiateCheckout ────────────────────────────────────────────────

    public function trackInitiateCheckout(float $value, int $numItems, array $contentIds = [], ?Request $request = null, ?string $eventId = null): void
    {
        $this->sendFBCAPIEvent('InitiateCheckout', [
            'content_ids' => $contentIds,
            'content_type' => 'product',
            'value' => $value,
            'currency' => 'INR',
            'num_items' => $numItems,
        ], $request, auth()->user(), $eventId);
    }

    // ─── AddPaymentInfo ──────────────────────────────────────────────────

    public function trackAddPaymentInfo(float $value, string $paymentMethod, ?Request $request = null, ?string $eventId = null): void
    {
        $this->sendFBCAPIEvent('AddPaymentInfo', [
            'value' => $value,
            'currency' => 'INR',
            'content_category' => $paymentMethod,
        ], $request, auth()->user(), $eventId);
    }

    // ─── Search ──────────────────────────────────────────────────────────

    public function trackSearch(string $searchString, int $resultsCount = 0, ?Request $request = null, ?string $eventId = null): void
    {
        $this->sendFBCAPIEvent('Search', [
            'search_string' => $searchString,
            'content_type' => 'product',
            'contents' => [],
        ], $request, auth()->user(), $eventId);
    }

    // ─── AddToWishlist ───────────────────────────────────────────────────

    public function trackAddToWishlist(Product $product, ?Request $request = null, ?string $eventId = null): void
    {
        $this->sendFBCAPIEvent('AddToWishlist', [
            'content_ids' => [(string) $product->id],
            'content_name' => $product->name,
            'content_type' => 'product',
            'value' => (float) $product->price,
            'currency' => 'INR',
        ], $request, auth()->user(), $eventId);
    }

    // ─── CompleteRegistration ────────────────────────────────────────────

    public function trackCompleteRegistration(?User $user = null, ?Request $request = null, ?string $eventId = null): void
    {
        $this->sendFBCAPIEvent('CompleteRegistration', [
            'content_name' => 'registration',
            'status' => true,
        ], $request, $user, $eventId);
    }

    // ─── Contact ─────────────────────────────────────────────────────────

    public function trackContact(?Request $request = null, ?string $eventId = null): void
    {
        $this->sendFBCAPIEvent('Contact', [
            'content_name' => 'contact_form',
        ], $request, auth()->user(), $eventId);
    }

    // ─── Subscribe ───────────────────────────────────────────────────────

    public function trackSubscribe(string $email, ?Request $request = null, ?string $eventId = null): void
    {
        $userData = [
            'em' => [hash('sha256', strtolower(trim($email)))],
        ];

        $this->sendFBCAPIEvent('Subscribe', [
            'content_name' => 'newsletter',
        ], $request, null, $eventId, $userData);
    }

    // ─── Generic event ───────────────────────────────────────────────────

    public function trackEvent(string $event, array $params = [], ?User $user = null, ?Request $request = null, ?string $eventId = null): void
    {
        $this->sendGA4Event($event, $params, $user);
        $this->sendFBCAPIEvent($event, $params, $request, $user, $eventId);
    }

    // =====================================================================
    // Facebook Conversions API (CAPI)
    // =====================================================================

    private function sendFBCAPIEvent(string $eventName, array $customData, ?Request $request = null, ?User $user = null, ?string $eventId = null, array $extraUserData = []): void
    {
        $pixelId = config('services.facebook.pixel_id');
        $accessToken = config('services.facebook.access_token');

        if (!$pixelId || !$accessToken) {
            return;
        }

        // Build user data with hashing
        $userData = $this->buildUserData($request, $user);
        $userData = array_merge($userData, $extraUserData);

        $event = [
            'event_name' => $eventName,
            'event_time' => now()->timestamp,
            'action_source' => 'website',
            'event_source_url' => $request?->fullUrl() ?? config('app.url'),
            'user_data' => $userData,
            'custom_data' => $customData,
        ];

        if ($eventId) {
            $event['event_id'] = $eventId;
        }

        $payload = ['data' => [$event]];

        $testCode = config('services.facebook.test_event_code');
        if ($testCode) {
            $payload['test_event_code'] = $testCode;
        }

        dispatch(function () use ($pixelId, $accessToken, $payload, $eventName) {
            try {
                Http::timeout(5)->post(
                    "https://graph.facebook.com/v21.0/{$pixelId}/events?access_token={$accessToken}",
                    $payload
                );
            } catch (\Throwable $e) {
                Log::warning("Facebook CAPI '{$eventName}' failed", ['error' => $e->getMessage()]);
            }
        })->afterResponse();
    }

    private function sendFBPurchaseEvent(Order $order, ?Request $request = null, ?string $eventId = null): void
    {
        $pixelId = config('services.facebook.pixel_id');
        $accessToken = config('services.facebook.access_token');

        if (!$pixelId || !$accessToken) {
            return;
        }

        $user = $order->user;
        $contents = $order->items->map(fn ($item) => [
            'id' => (string) $item->product_id,
            'quantity' => $item->quantity,
            'item_price' => (float) $item->price,
        ])->toArray();

        $userData = $this->buildUserData($request, $user);

        // Add order-level user data (guest orders)
        if (!$user && $order->guest_email) {
            $userData['em'] = [hash('sha256', strtolower(trim($order->guest_email)))];
        }
        if (!$user && $order->guest_phone) {
            $userData['ph'] = [hash('sha256', preg_replace('/\D/', '', $order->guest_phone))];
        }

        $event = [
            'event_name' => 'Purchase',
            'event_time' => now()->timestamp,
            'action_source' => 'website',
            'event_source_url' => $request?->fullUrl() ?? config('app.url'),
            'user_data' => $userData,
            'custom_data' => [
                'currency' => 'INR',
                'value' => (float) $order->total,
                'content_type' => 'product',
                'contents' => $contents,
                'content_ids' => $order->items->pluck('product_id')->map(fn ($id) => (string) $id)->toArray(),
                'order_id' => $order->order_number,
                'num_items' => $order->items->sum('quantity'),
            ],
        ];

        if ($eventId) {
            $event['event_id'] = $eventId;
        }

        $payload = ['data' => [$event]];

        $testCode = config('services.facebook.test_event_code');
        if ($testCode) {
            $payload['test_event_code'] = $testCode;
        }

        dispatch(function () use ($pixelId, $accessToken, $payload) {
            try {
                Http::timeout(5)->post(
                    "https://graph.facebook.com/v21.0/{$pixelId}/events?access_token={$accessToken}",
                    $payload
                );
            } catch (\Throwable $e) {
                Log::warning('Facebook CAPI Purchase failed', ['error' => $e->getMessage()]);
            }
        })->afterResponse();
    }

    /**
     * Build hashed user_data for CAPI from request + user model.
     */
    private function buildUserData(?Request $request, ?User $user): array
    {
        $data = [];

        // User model data
        if ($user) {
            $data['em'] = [hash('sha256', strtolower(trim($user->email)))];
            if ($user->phone) {
                $data['ph'] = [hash('sha256', preg_replace('/\D/', '', $user->phone))];
            }
            if ($user->first_name) {
                $data['fn'] = [hash('sha256', strtolower(trim($user->first_name)))];
            }
            if ($user->last_name) {
                $data['ln'] = [hash('sha256', strtolower(trim($user->last_name)))];
            }
            $data['external_id'] = [hash('sha256', (string) $user->id)];
        }

        // Request data
        if ($request) {
            $data['client_ip_address'] = $request->ip();
            $data['client_user_agent'] = $request->userAgent();

            // Facebook browser cookies for matching
            $fbp = $request->cookie('_fbp');
            if ($fbp) {
                $data['fbp'] = $fbp;
            }
            $fbc = $request->cookie('_fbc');
            if ($fbc) {
                $data['fbc'] = $fbc;
            }
        }

        $data['country'] = [hash('sha256', 'in')];

        return $data;
    }

    // =====================================================================
    // Google Analytics 4 (Measurement Protocol)
    // =====================================================================

    private function sendGA4PurchaseEvent(Order $order): void
    {
        $measurementId = config('services.ga4.measurement_id');
        $apiSecret = config('services.ga4.api_secret');

        if (!$measurementId || !$apiSecret) {
            return;
        }

        $items = $order->items->map(fn ($item) => [
            'item_id' => $item->sku ?? (string) $item->product_id,
            'item_name' => $item->product_name,
            'price' => (float) $item->price,
            'quantity' => $item->quantity,
        ])->toArray();

        $payload = [
            'client_id' => 'server.' . $order->user_id,
            'events' => [[
                'name' => 'purchase',
                'params' => [
                    'transaction_id' => $order->order_number,
                    'value' => (float) $order->total,
                    'currency' => 'INR',
                    'tax' => (float) $order->tax,
                    'shipping' => (float) $order->shipping_cost,
                    'items' => $items,
                ],
            ]],
        ];

        dispatch(function () use ($measurementId, $apiSecret, $payload) {
            try {
                Http::post("https://www.google-analytics.com/mp/collect?measurement_id={$measurementId}&api_secret={$apiSecret}", $payload);
            } catch (\Throwable $e) {
                Log::warning('GA4 Measurement Protocol failed', ['error' => $e->getMessage()]);
            }
        })->afterResponse();
    }

    private function sendGA4Event(string $eventName, array $params, ?User $user): void
    {
        $measurementId = config('services.ga4.measurement_id');
        $apiSecret = config('services.ga4.api_secret');

        if (!$measurementId || !$apiSecret) {
            return;
        }

        $clientId = $user ? 'server.' . $user->id : 'server.' . uniqid();

        $payload = [
            'client_id' => $clientId,
            'events' => [[
                'name' => $eventName,
                'params' => $params,
            ]],
        ];

        dispatch(function () use ($measurementId, $apiSecret, $payload, $eventName) {
            try {
                Http::post("https://www.google-analytics.com/mp/collect?measurement_id={$measurementId}&api_secret={$apiSecret}", $payload);
            } catch (\Throwable $e) {
                Log::warning("GA4 event '{$eventName}' failed", ['error' => $e->getMessage()]);
            }
        })->afterResponse();
    }
}
