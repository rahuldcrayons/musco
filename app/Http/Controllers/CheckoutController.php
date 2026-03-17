<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\AbandonedCheckout;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\UserAddress;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $isGuest = !auth()->check();
        $addresses = $isGuest ? collect() : UserAddress::where('user_id', auth()->id())->get();
        $defaultAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();

        $paymentSettings = Setting::where('group', 'payment')->pluck('value', 'key');

        // Fetch available coupons for display
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
            ->orderByDesc('value')
            ->get();

        // Check if weekend for prepaid bonus
        $isWeekend = now()->isWeekend();

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

        return view('checkout.index', compact(
            'cart', 'addresses', 'defaultAddress', 'paymentSettings',
            'isGuest', 'availableCoupons', 'isWeekend', 'fbEventId'
        ));
    }

    public function process(Request $request): RedirectResponse
    {
        $isGuest = !auth()->check();

        $rules = [
            'same_billing_address' => ['nullable', 'boolean'],
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        if ($isGuest) {
            $rules['guest_email'] = ['required', 'email', 'max:255'];
            $rules['guest_name'] = ['required', 'string', 'max:255'];
            $rules['guest_phone'] = ['required', 'string', 'max:20'];
            $rules['shipping_name'] = ['required', 'string', 'max:255'];
            $rules['shipping_phone'] = ['required', 'string', 'max:20'];
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
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Re-validate stock
        foreach ($cart->items as $item) {
            $available = $item->variant_id
                ? $item->variant->stock_quantity
                : $item->product->stock_quantity;

            if ($available < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "\"{$item->product->name}\" only has {$available} item(s) in stock. Please update your cart.");
            }
        }

        // Build address snapshots
        if ($isGuest) {
            $shippingSnapshot = [
                'name' => $validated['shipping_name'],
                'phone' => $validated['shipping_phone'],
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

        // Weekend prepaid discount: 5% extra on weekends for non-COD
        $paymentMethod = $validated['payment_method'];
        $weekendDiscount = 0;
        if (now()->isWeekend() && $paymentMethod !== 'cod') {
            $weekendDiscount = round(($cart->subtotal - $cart->discount) * 0.05, 2);
        }

        $totalDiscount = $cart->discount + $weekendDiscount;
        $finalTotal = max(0, $cart->subtotal - $totalDiscount);

        // COD partial payment: Rs100 upfront, rest on delivery
        $codAdvance = 0;
        if ($paymentMethod === 'cod') {
            $codAdvance = min(100, $finalTotal);
        }

        $order = DB::transaction(function () use ($cart, $shippingSnapshot, $billingSnapshot, $shippingAddressId, $billingAddressId, $validated, $isGuest, $finalTotal, $totalDiscount, $paymentMethod, $weekendDiscount, $codAdvance) {
            $metadata = ['payment_method' => $paymentMethod];
            if ($weekendDiscount > 0) {
                $metadata['weekend_discount'] = $weekendDiscount;
            }
            if ($codAdvance > 0) {
                $metadata['cod_advance'] = $codAdvance;
                $metadata['cod_balance'] = $finalTotal - $codAdvance;
            }

            $order = Order::create([
                'user_id' => $isGuest ? null : auth()->id(),
                'guest_email' => $validated['guest_email'] ?? null,
                'guest_name' => $validated['guest_name'] ?? null,
                'guest_phone' => $validated['guest_phone'] ?? null,
                'status' => 'confirmed',
                'payment_status' => 'pending',
                'subtotal' => $cart->subtotal,
                'discount' => $totalDiscount,
                'shipping_cost' => 0,
                'tax' => 0,
                'total' => $finalTotal,
                'paid_amount' => $codAdvance,
                'coupon_id' => $cart->coupon_id,
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

                if ($item->variant_id) {
                    $item->variant->decrement('stock_quantity', $item->quantity);
                } else {
                    $item->product->decrement('stock_quantity', $item->quantity);
                }

                $item->product->increment('sales_count', $item->quantity);
            }

            if ($cart->coupon) {
                $cart->coupon->increment('times_used');
            }

            $cart->items()->delete();
            $cart->update(['coupon_id' => null, 'discount' => 0]);

            return $order;
        });

        // Mark abandoned checkout as recovered
        $this->markAbandonedRecovered($cart);

        if ($isGuest) {
            session()->put('guest_order_id', $order->id);
        }

        OrderPlaced::dispatch($order, 'web');

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order): View
    {
        if (auth()->check()) {
            abort_unless($order->user_id === auth()->id(), 403);
        } else {
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
        $isGuest = !auth()->check();

        $rules = [
            'same_billing_address' => ['nullable', 'boolean'],
            'payment_method' => ['required', 'string', 'in:razorpay,upi'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        if ($isGuest) {
            $rules['guest_email'] = ['required', 'email', 'max:255'];
            $rules['guest_name'] = ['required', 'string', 'max:255'];
            $rules['guest_phone'] = ['required', 'string', 'max:20'];
            $rules['shipping_name'] = ['required', 'string', 'max:255'];
            $rules['shipping_phone'] = ['required', 'string', 'max:20'];
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

        // Calculate total
        $paymentMethod = $validated['payment_method'];
        $weekendDiscount = 0;
        if (now()->isWeekend() && $paymentMethod !== 'cod') {
            $weekendDiscount = round(($cart->subtotal - $cart->discount) * 0.05, 2);
        }
        $totalDiscount = $cart->discount + $weekendDiscount;
        $finalTotal = max(0, $cart->subtotal - $totalDiscount);
        $amountInPaise = (int) round($finalTotal * 100);

        // Create Razorpay order via REST API
        $response = Http::withBasicAuth(
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
            return response()->json(['error' => 'Failed to create payment order. Please try again.'], 500);
        }

        $razorpayOrder = $response->json();

        // Store checkout data in session for verification step
        session()->put('razorpay_checkout', [
            'razorpay_order_id' => $razorpayOrder['id'],
            'validated' => $validated,
            'final_total' => $finalTotal,
            'total_discount' => $totalDiscount,
            'weekend_discount' => $weekendDiscount,
            'payment_method' => $paymentMethod,
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
            'name' => config('app.name', 'Jikra'),
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
            return response()->json(['error' => 'Payment verification failed.'], 422);
        }

        // Payment verified - create the order
        $validated = $checkoutData['validated'];
        $isGuest = !auth()->check();
        $cart = $this->getCart(['items.product', 'items.variant', 'coupon']);

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Your cart is empty.'], 422);
        }

        // Build address snapshots
        if ($isGuest) {
            $shippingSnapshot = [
                'name' => $validated['shipping_name'],
                'phone' => $validated['shipping_phone'],
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
        $weekendDiscount = $checkoutData['weekend_discount'];
        $paymentMethod = $checkoutData['payment_method'];

        $order = DB::transaction(function () use ($cart, $shippingSnapshot, $billingSnapshot, $shippingAddressId, $billingAddressId, $validated, $isGuest, $finalTotal, $totalDiscount, $paymentMethod, $weekendDiscount, $request) {
            $metadata = [
                'payment_method' => $paymentMethod,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
            ];
            if ($weekendDiscount > 0) {
                $metadata['weekend_discount'] = $weekendDiscount;
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
                'shipping_cost' => 0,
                'tax' => 0,
                'total' => $finalTotal,
                'paid_amount' => $finalTotal,
                'coupon_id' => $cart->coupon_id,
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

                if ($item->variant_id) {
                    $item->variant->decrement('stock_quantity', $item->quantity);
                } else {
                    $item->product->decrement('stock_quantity', $item->quantity);
                }

                $item->product->increment('sales_count', $item->quantity);
            }

            if ($cart->coupon) {
                $cart->coupon->increment('times_used');
            }

            $cart->items()->delete();
            $cart->update(['coupon_id' => null, 'discount' => 0]);

            return $order;
        });

        // Clean up
        session()->forget('razorpay_checkout');
        $this->markAbandonedRecovered($cart);

        if ($isGuest) {
            session()->put('guest_order_id', $order->id);
        }

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

    private function markAbandonedRecovered(Cart $cart): void
    {
        AbandonedCheckout::where('cart_id', $cart->id)->update(['recovered' => true]);
    }
}
