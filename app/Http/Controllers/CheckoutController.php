<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\AbandonedCheckout;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Affiliate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\UserAddress;
use App\Services\AnalyticsService;
use App\Services\DelhiveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    private function logActivity(string $event, array $details = [], ?Request $request = null): void
    {
        try {
            $r = $request ?? request();
            DB::table('customer_activity_logs')->insert([
                'session_id' => session()->getId(),
                'user_id' => auth()->id(),
                'guest_email' => $r->input('guest_email'),
                'guest_phone' => $r->input('guest_phone'),
                'event' => $event,
                'details' => json_encode($details),
                'ip_address' => $r->ip(),
                'user_agent' => $r->userAgent(),
                'page_url' => $r->fullUrl(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Activity log failed', ['event' => $event, 'error' => $e->getMessage()]);
        }
    }

    public function index(): View|RedirectResponse
    {
        $this->logActivity('checkout_viewed');
        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $isGuest = !auth()->check();
        $addresses = $isGuest ? collect() : UserAddress::where('user_id', auth()->id())->get();
        $defaultAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();

        $paymentSettings = Setting::where('group', 'payment')->pluck('value', 'key');

        // Fetch only coupons that are valid for this cart's subtotal
        $cartSubtotal = $cart->subtotal;
        $availableCoupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereColumn('times_used', '<', 'usage_limit');
            })
            ->where(function ($q) use ($cartSubtotal) {
                $q->where('min_order_amount', '<=', $cartSubtotal)
                  ->orWhere('min_order_amount', 0);
            })
            ->orderByDesc('value')
            ->get();

        // Navratri offer active check
        $navratriActive = Setting::get('navratri_offer_active', '1') === '1';

        // Shipping fee calculation for display
        $freeShipThreshold = (float) Setting::get('free_shipping_threshold', 499);
        $shippingFee = ($cart->subtotal - $cart->discount) >= $freeShipThreshold ? 0 : 50;

        // Record abandoned checkout
        $this->recordAbandonedCheckout($cart, 'checkout');

        // Facebook CAPI: InitiateCheckout
        $fbEventId = AnalyticsService::generateEventId('ic');
        $contentIds = $cart->items->pluck('product_id')->map(fn ($id) => (string) $id)->toArray();
        app(AnalyticsService::class)->trackInitiateCheckout(
            (float) ($cart->subtotal - $cart->discount),
            $cart->items->sum('quantity'),
            $contentIds,
            request(),
            $fbEventId
        );

        // One-click checkout: check if user has preferences saved
        $oneClickReady = false;
        $checkoutPreference = null;
        if (!$isGuest) {
            $checkoutPreference = \App\Models\UserCheckoutPreference::where('user_id', auth()->id())->first();
            $oneClickReady = $checkoutPreference
                && $checkoutPreference->enable_one_click
                && $checkoutPreference->default_shipping_address_id
                && $defaultAddress;
        }

        // Loyalty points
        $loyaltyPoints = 0;
        $loyaltyValue = 0;
        $loyaltyEnabled = (bool) Setting::get('loyalty_enabled', true);
        if (!$isGuest && $loyaltyEnabled) {
            $loyaltyPoints = auth()->user()->loyalty_points_balance ?? 0;
            $loyaltyValue = round($loyaltyPoints * (float) Setting::get('loyalty_redeem_rate', 0.25), 2);
        }

        return view('checkout.index', compact(
            'cart', 'addresses', 'defaultAddress', 'paymentSettings',
            'isGuest', 'availableCoupons', 'navratriActive', 'fbEventId',
            'oneClickReady', 'checkoutPreference', 'loyaltyPoints', 'loyaltyValue'
        ));
    }

    public function process(Request $request): RedirectResponse
    {
        // One-click checkout fallback (should not normally be called for PayPal/Stripe redirect flows)
        return redirect()->route('checkout.index');
    }

    // ─── PayPal ──────────────────────────────────────────────────────────────

    public function createPaypalOrder(Request $request): JsonResponse
    {
        $this->logActivity('payment_initiated', ['method' => 'paypal'], $request);
        $isGuest = !auth()->check();

        $validated = $this->validateCheckoutForm($request);

        $cart = $this->getCart(['items.product', 'items.variant', 'coupon']);
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Your cart is empty.'], 422);
        }

        foreach ($cart->items as $item) {
            $available = $item->variant_id ? $item->variant->stock_quantity : $item->product->stock_quantity;
            if ($available < $item->quantity) {
                return response()->json(['error' => "\"{$item->product->name}\" only has {$available} item(s) in stock."], 422);
            }
        }

        $totals = $this->calculateTotals($cart);
        $addresses = $this->buildAddressSnapshots($validated, $isGuest);

        $accessToken = $this->getPaypalToken();
        if (!$accessToken) {
            return response()->json(['error' => 'Payment service unavailable. Please try again.'], 503);
        }

        $baseUrl = config('services.paypal.mode', 'live') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $paypalResponse = Http::timeout(15)->withToken($accessToken)
            ->post("{$baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'GBP',
                        'value' => number_format($totals['finalTotal'], 2, '.', ''),
                    ],
                    'description' => 'Trendymus Order',
                ]],
                'application_context' => [
                    'return_url' => route('checkout.paypal.success'),
                    'cancel_url' => route('checkout.failed'),
                    'brand_name' => 'Trendymus',
                    'landing_page' => 'LOGIN',
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        if ($paypalResponse->failed()) {
            Log::error('PayPal order creation failed', ['response' => $paypalResponse->json()]);
            return response()->json(['error' => 'Could not create PayPal order. Please try again.'], 500);
        }

        $paypalOrder = $paypalResponse->json();
        $approvalUrl = collect($paypalOrder['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        if (!$approvalUrl) {
            return response()->json(['error' => 'Could not get PayPal approval URL.'], 500);
        }

        session()->put('paypal_checkout', [
            'paypal_order_id' => $paypalOrder['id'],
            'validated'       => $validated,
            'final_total'     => $totals['finalTotal'],
            'total_discount'  => $totals['totalDiscount'],
            'shipping_fee'    => $totals['shippingFee'],
            'cart_subtotal'   => $cart->subtotal,
            'is_guest'        => $isGuest,
            'addresses'       => $addresses,
        ]);

        return response()->json(['approval_url' => $approvalUrl]);
    }

    public function paypalReturn(Request $request): RedirectResponse
    {
        $token = $request->query('token');
        $checkoutData = session('paypal_checkout');

        if (!$checkoutData || !$token) {
            return redirect()->route('checkout.failed');
        }

        // Idempotency check
        $existingOrder = Order::where('paypal_order_id', $token)->first();
        if ($existingOrder) {
            session()->forget('paypal_checkout');
            session()->put('guest_order_id', $existingOrder->id);
            return redirect()->route('checkout.success', $existingOrder);
        }

        $accessToken = $this->getPaypalToken();
        if (!$accessToken) {
            return redirect()->route('checkout.failed');
        }

        $baseUrl = config('services.paypal.mode', 'live') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $captureResponse = Http::timeout(15)->withToken($accessToken)
            ->post("{$baseUrl}/v2/checkout/orders/{$token}/capture", []);

        if ($captureResponse->failed() || ($captureResponse->json('status') !== 'COMPLETED')) {
            Log::error('PayPal capture failed', ['token' => $token, 'response' => $captureResponse->json()]);
            return redirect()->route('checkout.failed');
        }

        $cart = $this->getCart(['items.product', 'items.variant', 'coupon']);
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('checkout.failed');
        }

        try {
            $order = $this->createOrderInDb(
                $cart,
                $checkoutData['validated'],
                $checkoutData['is_guest'],
                $checkoutData['addresses'],
                $checkoutData['final_total'],
                $checkoutData['total_discount'],
                $checkoutData['shipping_fee'],
                'paypal',
                'paid',
                ['paypal_order_id' => $token]
            );
        } catch (\Exception $e) {
            Log::error('Order creation after PayPal failed', ['error' => $e->getMessage()]);
            return redirect()->route('checkout.failed');
        }

        session()->forget('paypal_checkout');
        $this->markAbandonedRecovered($cart, $order);

        if ($checkoutData['is_guest']) {
            session()->put('guest_order_id', $order->id);
            $this->createAccountForGuest($order, $checkoutData['validated']);
        } else {
            \App\Models\UserCheckoutPreference::updateOrCreate(
                ['user_id' => auth()->id()],
                ['default_shipping_address_id' => $checkoutData['addresses']['shipping_id'], 'default_payment_method' => 'paypal', 'same_as_shipping' => true, 'enable_one_click' => true]
            );
        }

        OrderPlaced::dispatch($order, 'web');

        return redirect()->route('checkout.success', $order);
    }

    // ─── Stripe ──────────────────────────────────────────────────────────────

    public function createStripeSession(Request $request): JsonResponse
    {
        $this->logActivity('payment_initiated', ['method' => 'stripe'], $request);
        $isGuest = !auth()->check();

        $validated = $this->validateCheckoutForm($request);

        $cart = $this->getCart(['items.product', 'items.variant', 'coupon']);
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Your cart is empty.'], 422);
        }

        foreach ($cart->items as $item) {
            $available = $item->variant_id ? $item->variant->stock_quantity : $item->product->stock_quantity;
            if ($available < $item->quantity) {
                return response()->json(['error' => "\"{$item->product->name}\" only has {$available} item(s) in stock."], 422);
            }
        }

        $totals = $this->calculateTotals($cart);
        $addresses = $this->buildAddressSnapshots($validated, $isGuest);
        $sessionKey = uniqid('s_', true);
        $customerEmail = $isGuest ? ($validated['guest_email'] ?? null) : auth()->user()?->email;

        $payload = [
            'mode' => 'payment',
            'line_items[0][price_data][currency]' => 'gbp',
            'line_items[0][price_data][unit_amount]' => (int) round($totals['finalTotal'] * 100),
            'line_items[0][price_data][product_data][name]' => 'Trendymus Order',
            'line_items[0][price_data][product_data][description]' => $cart->items->count() . ' item(s)',
            'line_items[0][quantity]' => 1,
            'success_url' => route('checkout.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}&key=' . $sessionKey,
            'cancel_url' => route('checkout.failed'),
        ];
        if ($customerEmail) {
            $payload['customer_email'] = $customerEmail;
        }

        $stripeResponse = Http::timeout(15)
            ->withBasicAuth(config('services.stripe.secret'), '')
            ->asForm()
            ->post('https://api.stripe.com/v1/checkout/sessions', $payload);

        if ($stripeResponse->failed()) {
            Log::error('Stripe session creation failed', ['response' => $stripeResponse->json()]);
            return response()->json(['error' => 'Could not create payment session. Please try again.'], 500);
        }

        $stripeSession = $stripeResponse->json();
        $checkoutUrl = $stripeSession['url'] ?? null;

        if (!$checkoutUrl) {
            return response()->json(['error' => 'Could not get payment URL.'], 500);
        }

        session()->put("stripe_{$sessionKey}", [
            'stripe_session_id' => $stripeSession['id'],
            'validated'         => $validated,
            'final_total'       => $totals['finalTotal'],
            'total_discount'    => $totals['totalDiscount'],
            'shipping_fee'      => $totals['shippingFee'],
            'cart_subtotal'     => $cart->subtotal,
            'is_guest'          => $isGuest,
            'addresses'         => $addresses,
        ]);

        return response()->json(['checkout_url' => $checkoutUrl]);
    }

    public function stripeSuccess(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');
        $sessionKey = $request->query('key');
        $checkoutData = session("stripe_{$sessionKey}");

        if (!$sessionId || !$checkoutData) {
            return redirect()->route('checkout.failed');
        }

        // Idempotency check
        $existingOrder = Order::where('stripe_session_id', $sessionId)->first();
        if ($existingOrder) {
            session()->forget("stripe_{$sessionKey}");
            session()->put('guest_order_id', $existingOrder->id);
            return redirect()->route('checkout.success', $existingOrder);
        }

        $stripeResponse = Http::timeout(15)
            ->withBasicAuth(config('services.stripe.secret'), '')
            ->get("https://api.stripe.com/v1/checkout/sessions/{$sessionId}");

        if ($stripeResponse->failed() || $stripeResponse->json('payment_status') !== 'paid') {
            Log::error('Stripe payment verification failed', ['session_id' => $sessionId, 'response' => $stripeResponse->json()]);
            return redirect()->route('checkout.failed');
        }

        $stripeSession = $stripeResponse->json();

        $cart = $this->getCart(['items.product', 'items.variant', 'coupon']);
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('checkout.failed');
        }

        try {
            $order = $this->createOrderInDb(
                $cart,
                $checkoutData['validated'],
                $checkoutData['is_guest'],
                $checkoutData['addresses'],
                $checkoutData['final_total'],
                $checkoutData['total_discount'],
                $checkoutData['shipping_fee'],
                'stripe',
                'paid',
                [
                    'stripe_session_id'      => $sessionId,
                    'stripe_payment_intent'  => $stripeSession['payment_intent'] ?? null,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Order creation after Stripe failed', ['error' => $e->getMessage()]);
            return redirect()->route('checkout.failed');
        }

        session()->forget("stripe_{$sessionKey}");
        $this->markAbandonedRecovered($cart, $order);

        if ($checkoutData['is_guest']) {
            session()->put('guest_order_id', $order->id);
            $this->createAccountForGuest($order, $checkoutData['validated']);
        } else {
            \App\Models\UserCheckoutPreference::updateOrCreate(
                ['user_id' => auth()->id()],
                ['default_shipping_address_id' => $checkoutData['addresses']['shipping_id'], 'default_payment_method' => 'stripe', 'same_as_shipping' => true, 'enable_one_click' => true]
            );
        }

        OrderPlaced::dispatch($order, 'web');

        return redirect()->route('checkout.success', $order);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function validateCheckoutForm(Request $request): array
    {
        $isGuest = !auth()->check();

        $rules = [
            'payment_method' => ['required', 'string', 'in:paypal,stripe'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ];

        if ($isGuest) {
            $rules['guest_email']             = ['required', 'email', 'max:255'];
            $rules['guest_name']              = ['required', 'string', 'max:255'];
            $rules['guest_phone']             = ['required', 'string', 'max:20'];
            $rules['shipping_name']           = ['required', 'string', 'max:255'];
            $rules['shipping_address_line_1'] = ['required', 'string', 'max:255'];
            $rules['shipping_address_line_2'] = ['nullable', 'string', 'max:255'];
            $rules['shipping_city']           = ['required', 'string', 'max:100'];
            $rules['shipping_state']          = ['nullable', 'string', 'max:100'];
            $rules['shipping_postal_code']    = ['required', 'string', 'max:10'];
        } else {
            $rules['shipping_address_id'] = ['required', 'exists:user_addresses,id'];
            $rules['billing_address_id']  = ['nullable', 'exists:user_addresses,id'];
            $rules['same_billing_address'] = ['nullable', 'boolean'];
        }

        return $request->validate($rules);
    }

    private function buildAddressSnapshots(array $validated, bool $isGuest): array
    {
        if ($isGuest) {
            $snapshot = [
                'name'             => $validated['shipping_name'],
                'phone'            => $validated['guest_phone'] ?? '',
                'address_line_1'   => $validated['shipping_address_line_1'],
                'address_line_2'   => $validated['shipping_address_line_2'] ?? '',
                'city'             => $validated['shipping_city'],
                'state'            => $validated['shipping_state'] ?? '',
                'postal_code'      => $validated['shipping_postal_code'],
                'country'          => 'United Kingdom',
            ];
            return ['shipping' => $snapshot, 'billing' => $snapshot, 'shipping_id' => null, 'billing_id' => null];
        }

        $shippingAddress = UserAddress::where('user_id', auth()->id())->findOrFail($validated['shipping_address_id']);
        $billingId = ($validated['same_billing_address'] ?? true)
            ? $shippingAddress->id
            : ($validated['billing_address_id'] ?? $shippingAddress->id);
        $billingAddress = UserAddress::where('user_id', auth()->id())->findOrFail($billingId);

        return [
            'shipping' => [
                'name' => $shippingAddress->full_name, 'phone' => $shippingAddress->phone,
                'address_line_1' => $shippingAddress->address_line_1, 'address_line_2' => $shippingAddress->address_line_2,
                'city' => $shippingAddress->city, 'state' => $shippingAddress->state,
                'postal_code' => $shippingAddress->postal_code, 'country' => $shippingAddress->country,
            ],
            'billing' => [
                'name' => $billingAddress->full_name,
                'address_line_1' => $billingAddress->address_line_1,
                'city' => $billingAddress->city, 'state' => $billingAddress->state,
                'postal_code' => $billingAddress->postal_code, 'country' => $billingAddress->country,
            ],
            'shipping_id' => $shippingAddress->id,
            'billing_id'  => $billingId,
        ];
    }

    private function calculateTotals(Cart $cart): array
    {
        $freeShipThreshold = (float) Setting::get('free_shipping_threshold', 30);
        $defaultShippingFee = (float) Setting::get('shipping_fee', 3.99);
        $shippingFee = ($cart->subtotal - $cart->discount) >= $freeShipThreshold ? 0.0 : $defaultShippingFee;
        $totalDiscount = (float) $cart->discount;
        $finalTotal = round(max(0.0, $cart->subtotal - $totalDiscount + $shippingFee), 2);
        return compact('shippingFee', 'totalDiscount', 'finalTotal');
    }

    private function getPaypalToken(): ?string
    {
        $baseUrl = config('services.paypal.mode', 'live') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $response = Http::timeout(15)
            ->withBasicAuth(config('services.paypal.client_id'), config('services.paypal.client_secret'))
            ->asForm()
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        if ($response->failed()) {
            Log::error('PayPal token request failed', ['status' => $response->status()]);
            return null;
        }
        return $response->json('access_token');
    }

    private function createOrderInDb(Cart $cart, array $validated, bool $isGuest, array $addresses, float $finalTotal, float $totalDiscount, float $shippingFee, string $paymentMethod, string $paymentStatus, array $extra = []): Order
    {
        $affiliateId = null;
        $affiliateRefCode = null;
        $refCode = session('affiliate_ref') ?? request()->cookie(config('affiliate.cookie_name', 'musco_ref'));
        if ($refCode) {
            $affiliate = Affiliate::where('referral_code', $refCode)->where('status', 'approved')->first();
            if ($affiliate) {
                $affiliateId = $affiliate->id;
                $affiliateRefCode = $refCode;
            }
        }

        return DB::transaction(function () use ($cart, $validated, $isGuest, $addresses, $finalTotal, $totalDiscount, $shippingFee, $paymentMethod, $paymentStatus, $extra, $affiliateId, $affiliateRefCode) {
            DB::table('carts')->where('id', $cart->id)->lockForUpdate()->first();

            $metadata = array_merge(['payment_method' => $paymentMethod], $extra);
            if ($affiliateRefCode) {
                $metadata['affiliate_referral_code'] = $affiliateRefCode;
            }

            $order = Order::create([
                'user_id'                   => $isGuest ? null : auth()->id(),
                'guest_email'               => $validated['guest_email'] ?? null,
                'guest_name'                => $validated['guest_name'] ?? null,
                'guest_phone'               => $validated['guest_phone'] ?? null,
                'status'                    => 'confirmed',
                'payment_status'            => $paymentStatus,
                'payment_method'            => $paymentMethod,
                'subtotal'                  => $cart->subtotal,
                'discount'                  => $totalDiscount,
                'shipping_cost'             => $shippingFee,
                'tax'                       => 0,
                'total'                     => $finalTotal,
                'paid_amount'               => $paymentStatus === 'paid' ? $finalTotal : 0,
                'coupon_id'                 => $cart->coupon_id,
                'affiliate_id'              => $affiliateId,
                'affiliate_referral_code'   => $affiliateRefCode,
                'shipping_address_id'       => $addresses['shipping_id'],
                'billing_address_id'        => $addresses['billing_id'],
                'shipping_address_snapshot' => $addresses['shipping'],
                'billing_address_snapshot'  => $addresses['billing'],
                'notes'                     => $validated['notes'] ?? null,
                'metadata'                  => $metadata,
                'paypal_order_id'           => $extra['paypal_order_id'] ?? null,
                'stripe_session_id'         => $extra['stripe_session_id'] ?? null,
                'stripe_payment_intent'     => $extra['stripe_payment_intent'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $item->product_id,
                    'variant_id'  => $item->variant_id,
                    'seller_id'   => $item->product->seller_id,
                    'product_name' => $item->product->name,
                    'sku'         => $item->product->sku ?? '',
                    'variant_name' => $item->variant?->attributeValues->pluck('value')->join(' / '),
                    'quantity'    => $item->quantity,
                    'mrp'         => $item->product->mrp ?? $item->price,
                    'price'       => $item->price,
                    'tax'         => 0,
                    'discount'    => 0,
                    'total'       => $item->price * $item->quantity,
                ]);

                $qty = (int) $item->quantity;
                if ($item->variant_id) {
                    $updated = DB::table('product_variants')->where('id', $item->variant_id)->where('stock_quantity', '>=', $qty)->update(['stock_quantity' => DB::raw("stock_quantity - {$qty}")]);
                } else {
                    $updated = DB::table('products')->where('id', $item->product_id)->where('stock_quantity', '>=', $qty)->update(['stock_quantity' => DB::raw("stock_quantity - {$qty}")]);
                }
                if (!$updated) {
                    throw new \RuntimeException("Insufficient stock for \"{$item->product->name}\".");
                }
                $item->product->increment('sales_count', $item->quantity);
            }

            if ($cart->coupon) {
                $coupon = $cart->coupon;
                if ($coupon->is_active && (!$coupon->expires_at || $coupon->expires_at >= now()) && (!$coupon->usage_limit || $coupon->times_used < $coupon->usage_limit)) {
                    $coupon->increment('times_used');
                }
            }

            $cart->items()->delete();
            $cart->update(['coupon_id' => null, 'discount' => 0]);

            return $order;
        });
    }

    public function success(Order $order): View
    {
        if (auth()->check()) {
            abort_unless($order->user_id === auth()->id(), 403);
        } else {
            // Rate-limit guest order success page to prevent order ID enumeration
            $key = 'guest_order_view:' . request()->ip();
            abort_if(\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 10), 429);
            \Illuminate\Support\Facades\RateLimiter::hit($key, 300);

            abort_unless(session('guest_order_id') === $order->id, 403);
        }

        $order->load(['items.product']);

        // Generate event_id for Purchase dedup (shared between client fbq and server CAPI)
        $fbPurchaseEventId = AnalyticsService::generateEventId('pur');

        // Facebook CAPI: Purchase (server-side)
        app(AnalyticsService::class)->trackPurchase($order, request(), $fbPurchaseEventId);

        return view('checkout.success', compact('order', 'fbPurchaseEventId'));
    }

    public function failed(): View
    {
        return view('checkout.failed');
    }

    /**
     * Create a Razorpay order for inline checkout (AJAX).
     */
    public function createRazorpayOrder(Request $request): JsonResponse
    {
        $this->logActivity('payment_initiated', ['method' => $request->input('payment_method')], $request);
        $isGuest = !auth()->check();

        $rules = [
            'same_billing_address' => ['nullable', 'boolean'],
            'payment_method' => ['required', 'string', 'in:razorpay,upi,cod,partial_pay'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        if ($isGuest) {
            $rules['guest_email'] = ['required', 'email', 'max:255'];
            $rules['guest_name'] = ['required', 'string', 'max:255'];
            $rules['guest_phone'] = ['required', 'string', 'max:20'];
            $rules['shipping_name'] = ['required', 'string', 'max:255'];
            // shipping_phone uses guest_phone
            $rules['shipping_address_line_1'] = ['required', 'string', 'max:255'];
            $rules['shipping_address_line_2'] = ['nullable', 'string', 'max:255'];
            $rules['shipping_city'] = ['required', 'string', 'max:100'];
            $rules['shipping_state'] = ['required', 'string', 'max:100'];
            $rules['shipping_postal_code'] = ['required', 'string', 'max:10'];
        } else {
            $rules['shipping_address_id'] = ['required', 'exists:user_addresses,id'];
            $rules['billing_address_id'] = ['nullable', 'exists:user_addresses,id'];
        }

        $validated = $request->validate($rules);

        $cart = $this->getCart(['items.product', 'items.variant', 'coupon']);

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Your cart is empty.'], 422);
        }

        // Re-validate stock
        foreach ($cart->items as $item) {
            $available = $item->variant_id
                ? $item->variant->stock_quantity
                : $item->product->stock_quantity;

            if ($available < $item->quantity) {
                return response()->json([
                    'error' => "\"{$item->product->name}\" only has {$available} item(s) in stock.",
                ], 422);
            }
        }

        // Calculate total (same logic as COD flow)
        $paymentMethod = $validated['payment_method'];
        $navratriDiscount = 0;
        $navratriActive = Setting::get('navratri_offer_active', '1') === '1';
        if ($navratriActive) {
            $navratriDiscount = round(($cart->subtotal - $cart->discount) * 0.05, 2);
        }
        $totalDiscount = $cart->discount + $navratriDiscount;

        // Shipping fee
        $freeShipThreshold = (float) Setting::get('free_shipping_threshold', 499);
        $shippingFee = ($cart->subtotal - $cart->discount) >= $freeShipThreshold ? 0 : 50;

        $finalTotal = max(0, $cart->subtotal - $totalDiscount + $shippingFee);
        $amountInPaise = (int) round($finalTotal * 100);

        // Create Razorpay order via REST API
        try {
            $response = Http::timeout(15)->withBasicAuth(
                config('services.razorpay.key_id'),
                config('services.razorpay.key_secret')
            )->post('https://api.razorpay.com/v1/orders', [
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => 'cart_' . $cart->id . '_' . time(),
                'notes' => [
                    'cart_id' => $cart->id,
                    'user_id' => auth()->id() ?? 'guest',
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Razorpay order creation failed', ['response' => $response->json()]);
                $this->logActivity('payment_error', ['stage' => 'razorpay_order_create', 'error' => $response->json()], $request);
                return response()->json(['error' => 'Failed to create payment order. Please try again.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Razorpay order creation exception', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Payment service is temporarily unavailable. Please try again.'], 503);
        }

        $razorpayOrder = $response->json();

        // Store checkout data in session for verification step
        session()->put('razorpay_checkout', [
            'razorpay_order_id' => $razorpayOrder['id'],
            'validated' => $validated,
            'final_total' => $finalTotal,
            'total_discount' => $totalDiscount,
            'navratri_discount' => $navratriDiscount,
            'shipping_fee' => $shippingFee,
            'payment_method' => $paymentMethod,
            'cart_subtotal' => $cart->subtotal,
        ]);

        $contactName = $isGuest
            ? $validated['guest_name']
            : (auth()->user()->name ?? '');
        $contactEmail = $isGuest
            ? $validated['guest_email']
            : (auth()->user()->email ?? '');
        $contactPhone = $isGuest
            ? $validated['guest_phone']
            : (auth()->user()->phone ?? '');

        // Facebook CAPI: AddPaymentInfo
        $fbEventId = AnalyticsService::generateEventId('api');
        app(AnalyticsService::class)->trackAddPaymentInfo($finalTotal, $paymentMethod, $request, $fbEventId);

        return response()->json([
            'order_id' => $razorpayOrder['id'],
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'key' => config('services.razorpay.key_id'),
            'name' => config('app.name', 'MusCo'),
            'description' => 'Order from ' . config('app.name'),
            'prefill' => [
                'name' => $contactName,
                'email' => $contactEmail,
                'contact' => $contactPhone,
            ],
            'fb_event_id' => $fbEventId,
        ]);
    }

    /**
     * Verify Razorpay payment and create the order.
     */
    public function verifyRazorpayPayment(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $checkoutData = session('razorpay_checkout');
        if (!$checkoutData || $checkoutData['razorpay_order_id'] !== $request->razorpay_order_id) {
            return response()->json(['error' => 'Invalid session. Please try again.'], 422);
        }

        // Verify signature
        $expectedSignature = hash_hmac(
            'sha256',
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
            config('services.razorpay.key_secret')
        );

        if (!hash_equals($expectedSignature, $request->razorpay_signature)) {
            Log::warning('Razorpay signature verification failed', [
                'order_id' => $request->razorpay_order_id,
            ]);
            $this->logActivity('payment_verification_failed', ['razorpay_order_id' => $request->razorpay_order_id], $request);
            return response()->json(['error' => 'Payment verification failed.'], 422);
        }

        // Payment verified - create the order
        $validated = $checkoutData['validated'];
        $isGuest = !auth()->check();

        // Idempotency: check if order already created for this Razorpay order
        $existingOrder = Order::where('razorpay_order_id', $request->razorpay_order_id)->first();
        if ($existingOrder) {
            session()->forget('razorpay_checkout');
            return response()->json([
                'success' => true,
                'redirect' => route('checkout.success', $existingOrder),
            ]);
        }

        $cart = $this->getCart(['items.product', 'items.variant', 'coupon']);

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Your cart is empty.'], 422);
        }

        // Re-verify cart total matches what was charged
        if (abs($cart->subtotal - ($checkoutData['cart_subtotal'] ?? 0)) > 0.01) {
            Log::warning('Cart modified between payment creation and verification', [
                'expected_subtotal' => $checkoutData['cart_subtotal'],
                'actual_subtotal' => $cart->subtotal,
            ]);
            return response()->json(['error' => 'Cart was modified. Please try again.'], 422);
        }

        // Re-validate stock before creating order
        foreach ($cart->items as $item) {
            $available = $item->variant_id
                ? $item->variant->stock_quantity
                : $item->product->stock_quantity;
            if ($available < $item->quantity) {
                return response()->json([
                    'error' => "\"{$item->product->name}\" is now out of stock. Your payment will be refunded automatically.",
                ], 422);
            }
        }

        // Build address snapshots
        if ($isGuest) {
            $shippingSnapshot = [
                'name' => $validated['shipping_name'],
                'phone' => $validated['guest_phone'] ?? '',
                'address_line_1' => $validated['shipping_address_line_1'],
                'address_line_2' => $validated['shipping_address_line_2'] ?? '',
                'city' => $validated['shipping_city'],
                'state' => $validated['shipping_state'],
                'postal_code' => $validated['shipping_postal_code'],
                'country' => 'India',
            ];
            $billingSnapshot = $shippingSnapshot;
            $shippingAddressId = null;
            $billingAddressId = null;
        } else {
            $shippingAddress = UserAddress::where('user_id', auth()->id())->findOrFail($validated['shipping_address_id']);
            $billingAddressId = $validated['same_billing_address']
                ? $shippingAddress->id
                : ($validated['billing_address_id'] ?? $shippingAddress->id);
            $billingAddress = UserAddress::where('user_id', auth()->id())->findOrFail($billingAddressId);

            $shippingSnapshot = [
                'name' => $shippingAddress->full_name,
                'phone' => $shippingAddress->phone,
                'address_line_1' => $shippingAddress->address_line_1,
                'address_line_2' => $shippingAddress->address_line_2,
                'city' => $shippingAddress->city,
                'state' => $shippingAddress->state,
                'postal_code' => $shippingAddress->postal_code,
                'country' => $shippingAddress->country,
            ];
            $billingSnapshot = [
                'name' => $billingAddress->full_name,
                'address_line_1' => $billingAddress->address_line_1,
                'city' => $billingAddress->city,
                'state' => $billingAddress->state,
                'postal_code' => $billingAddress->postal_code,
                'country' => $billingAddress->country,
            ];
            $shippingAddressId = $shippingAddress->id;
            $billingAddressId = $billingAddress->id;
        }

        $finalTotal = $checkoutData['final_total'];
        $totalDiscount = $checkoutData['total_discount'];
        $navratriDiscount = $checkoutData['navratri_discount'];
        $shippingFee = $checkoutData['shipping_fee'] ?? 0;
        $paymentMethod = $checkoutData['payment_method'];

        // Resolve affiliate from cookie/session
        $affiliateId = null;
        $affiliateRefCode = null;
        $refCode = session('affiliate_ref') ?? request()->cookie(config('affiliate.cookie_name', 'musco_ref'));
        if ($refCode) {
            $razorpayAffiliate = Affiliate::where('referral_code', $refCode)->where('status', 'approved')->first();
            if ($razorpayAffiliate) {
                $affiliateId = $razorpayAffiliate->id;
                $affiliateRefCode = $refCode;
            }
        }

        $order = DB::transaction(function () use ($cart, $shippingSnapshot, $billingSnapshot, $shippingAddressId, $billingAddressId, $validated, $isGuest, $finalTotal, $totalDiscount, $paymentMethod, $navratriDiscount, $request, $affiliateId, $affiliateRefCode, $shippingFee) {
            $metadata = [
                'payment_method' => $paymentMethod,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
            ];
            if ($navratriDiscount > 0) {
                $metadata['navratri_discount'] = $navratriDiscount;
            }
            if ($affiliateRefCode) {
                $metadata['affiliate_referral_code'] = $affiliateRefCode;
            }

            $order = Order::create([
                'user_id' => $isGuest ? null : auth()->id(),
                'guest_email' => $validated['guest_email'] ?? null,
                'guest_name' => $validated['guest_name'] ?? null,
                'guest_phone' => $validated['guest_phone'] ?? null,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'subtotal' => $cart->subtotal,
                'discount' => $totalDiscount,
                'shipping_cost' => $shippingFee,
                'tax' => 0,
                'total' => $finalTotal,
                'paid_amount' => $finalTotal,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'coupon_id' => $cart->coupon_id,
                'affiliate_id' => $affiliateId,
                'affiliate_referral_code' => $affiliateRefCode,
                'shipping_address_id' => $shippingAddressId,
                'billing_address_id' => $billingAddressId,
                'shipping_address_snapshot' => $shippingSnapshot,
                'billing_address_snapshot' => $billingSnapshot,
                'notes' => $validated['notes'],
                'metadata' => $metadata,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'seller_id' => $item->product->seller_id,
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku ?? '',
                    'variant_name' => $item->variant?->attributeValues->pluck('value')->join(' / '),
                    'quantity' => $item->quantity,
                    'mrp' => $item->product->mrp ?? $item->price,
                    'price' => $item->price,
                    'tax' => 0,
                    'discount' => 0,
                    'total' => $item->price * $item->quantity,
                ]);

                // Atomic stock decrement — explicit int cast prevents any SQL injection risk.
                $qty = (int) $item->quantity;
                if ($item->variant_id) {
                    $updated = DB::table('product_variants')
                        ->where('id', $item->variant_id)
                        ->where('stock_quantity', '>=', $qty)
                        ->update(['stock_quantity' => DB::raw("stock_quantity - {$qty}")]);
                } else {
                    $updated = DB::table('products')
                        ->where('id', $item->product_id)
                        ->where('stock_quantity', '>=', $qty)
                        ->update(['stock_quantity' => DB::raw("stock_quantity - {$qty}")]);
                }

                if (!$updated) {
                    throw new \RuntimeException("Insufficient stock for \"{$item->product->name}\".");
                }

                $item->product->increment('sales_count', $item->quantity);
            }

            // Re-validate coupon
            if ($cart->coupon) {
                $coupon = $cart->coupon;
                if ($coupon->is_active && (!$coupon->expires_at || $coupon->expires_at >= now()) && (!$coupon->usage_limit || $coupon->times_used < $coupon->usage_limit)) {
                    $coupon->increment('times_used');
                }
            }

            $cart->items()->delete();
            $cart->update(['coupon_id' => null, 'discount' => 0]);

            return $order;
        });

        // Clean up
        session()->forget('razorpay_checkout');
        $this->markAbandonedRecovered($cart, $order);

        if ($isGuest) {
            session()->put('guest_order_id', $order->id);
        }

        $this->logActivity('order_placed', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total' => $order->total,
            'payment_method' => $paymentMethod,
            'razorpay_payment_id' => $request->razorpay_payment_id,
        ], $request);

        OrderPlaced::dispatch($order, 'web');

        return response()->json([
            'success' => true,
            'redirect' => route('checkout.success', $order),
        ]);
    }

    private function getCart(array $with = ['items.product', 'items.variant']): ?Cart
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())->with($with)->first();
        }

        $sessionId = session()->getId();
        return Cart::where('session_id', $sessionId)->whereNull('user_id')->with($with)->first();
    }

    private function recordAbandonedCheckout(Cart $cart, string $step = 'checkout'): void
    {
        AbandonedCheckout::updateOrCreate(
            ['cart_id' => $cart->id],
            [
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'cart_total' => $cart->subtotal - $cart->discount,
                'items_count' => $cart->items->count(),
                'step' => $step,
                'cart_snapshot' => $cart->items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ])->toArray(),
            ]
        );
    }

    private function markAbandonedRecovered(Cart $cart, Order $order): void
    {
        AbandonedCheckout::where('cart_id', $cart->id)->update([
            'recovered' => true,
            'order_id' => $order->id,
            'recovered_at' => now(),
        ]);
    }

    /**
     * Check pincode serviceability via Delhivery API.
     */
    public function checkPincode(string $pincode, DelhiveryService $delhivery): JsonResponse
    {
        $result = $delhivery->checkPincode($pincode);

        return response()->json($result);
    }

    /**
     * Capture guest email/phone for abandoned checkout recovery (AJAX).
     */
    public function captureAbandoned(Request $request): JsonResponse
    {
        $cart = $this->getCart();
        if (!$cart) {
            return response()->json(['ok' => false], 404);
        }

        $email = $request->input('email');
        $phone = $request->input('phone');
        $name = $request->input('name');

        if (!$email && !$phone) {
            return response()->json(['ok' => false], 422);
        }

        AbandonedCheckout::updateOrCreate(
            ['cart_id' => $cart->id],
            array_filter([
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'email' => $email,
                'phone' => $phone,
                'name' => $name,
                'cart_total' => $cart->subtotal - $cart->discount,
                'items_count' => $cart->items->count(),
                'step' => 'contact_captured',
                'cart_snapshot' => $cart->items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ])->toArray(),
            ])
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Auto-create user account for guest orders and send credentials via email + WhatsApp.
     */
    private function createAccountForGuest(Order $order, array $validated): void
    {
        $email = $validated['guest_email'] ?? null;
        $phone = $validated['guest_phone'] ?? null;
        $name = $validated['guest_name'] ?? 'Customer';

        if (!$email) {
            return;
        }

        // Check if account already exists
        if (\App\Models\User::where('email', $email)->exists()) {
            return;
        }

        try {
            $password = strtolower(substr(str_replace(' ', '', $name), 0, 4)) . rand(1000, 9999);
            $nameParts = explode(' ', $name, 2);

            $user = \App\Models\User::create([
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'email' => $email,
                'phone' => $phone ? preg_replace('/\D/', '', $phone) : null,
                'password' => bcrypt($password),
                'email_verified_at' => now(),
            ]);

            // Link order to new user
            $order->update(['user_id' => $user->id]);

            // Send credentials via email
            \Illuminate\Support\Facades\Mail::send([], [], function ($m) use ($email, $name, $password, $order) {
                $m->to($email)
                  ->from(config('mail.from.address', 'info@musco.com'), config('app.name', 'MusCo'))
                  ->subject('Your MusCo Account is Ready!')
                  ->html("<div style='font-family:sans-serif;max-width:450px;margin:0 auto;padding:20px;'>
                    <h2 style='color:#205258;'>Welcome to MusCo, {$name}!</h2>
                    <p style='font-size:14px;color:#333;'>Your account has been created with your recent order #{$order->order_number}.</p>
                    <div style='background:#f5f5f5;border-radius:8px;padding:15px;margin:15px 0;'>
                        <p style='font-size:13px;color:#555;margin:0 0 5px;'><strong>Email:</strong> {$email}</p>
                        <p style='font-size:13px;color:#555;margin:0 0 5px;'><strong>Password:</strong> {$password}</p>
                    </div>
                    <p style='font-size:13px;color:#333;'>You can also login using OTP via WhatsApp — no password needed!</p>
                    <a href='" . url('/login') . "' style='display:inline-block;background:#205258;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:bold;margin-top:10px;'>Login Now</a>
                    <p style='font-size:11px;color:#999;margin-top:20px;'>We recommend changing your password after first login.</p>
                  </div>");
            });

            // Send credentials via WhatsApp
            if ($phone) {
                $token = config('services.meta.page_access_token');
                $phoneId = config('services.meta.whatsapp_phone_number_id');
                if ($token && $phoneId) {
                    $cleanPhone = preg_replace('/\D/', '', $phone);
                    if (!str_starts_with($cleanPhone, '91') && strlen($cleanPhone) === 10) {
                        $cleanPhone = '91' . $cleanPhone;
                    }
                    Http::withToken($token)->post("https://graph.facebook.com/v21.0/{$phoneId}/messages", [
                        'messaging_product' => 'whatsapp',
                        'to' => $cleanPhone,
                        'type' => 'text',
                        'text' => [
                            'body' => "Hi {$name}! Your MusCo account is ready.\n\nEmail: {$email}\nPassword: {$password}\n\nYou can also login with OTP — just enter your phone number on the login page.\n\nLogin: " . url('/login'),
                        ],
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Guest account creation failed', ['email' => $email, 'error' => $e->getMessage()]);
        }
    }
}
