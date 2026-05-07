<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    /**
     * Handle incoming Stripe webhook events.
     *
     * URL: https://trendymus.com/webhook/stripe
     */
    public function handle(Request $request, StripeService $stripe)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature', '');

        // Verify webhook signature
        if (!$stripe->verifyWebhookSignature($payload, $sigHeader)) {
            Log::channel('stripe')->warning('Webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);
        $eventType = $event['type'] ?? 'unknown';
        $data = $event['data']['object'] ?? [];

        Log::channel('stripe')->info("Stripe Webhook: {$eventType}", [
            'id' => $event['id'] ?? null,
        ]);

        return match ($eventType) {
            'checkout.session.completed'     => $this->handleSessionCompleted($data),
            'payment_intent.succeeded'       => $this->handlePaymentSucceeded($data),
            'payment_intent.payment_failed'  => $this->handlePaymentFailed($data),
            'charge.refunded'                => $this->handleRefund($data),
            default                          => $this->handleUnknown($eventType),
        };
    }

    /**
     * checkout.session.completed - payment was successful via Stripe Checkout.
     */
    private function handleSessionCompleted(array $session)
    {
        $sessionId = $session['id'] ?? null;
        $paymentIntent = $session['payment_intent'] ?? null;
        $amountTotal = ($session['amount_total'] ?? 0) / 100; // Convert from pence
        $currency = strtoupper($session['currency'] ?? 'GBP');
        $customerEmail = $session['customer_email'] ?? $session['customer_details']['email'] ?? 'N/A';
        $reference = $session['client_reference_id'] ?? '';

        $order = $sessionId ? Order::where('stripe_session_id', $sessionId)->first() : null;

        if ($order) {
            $order->load(['items.product', 'customer']);

            // Update payment status
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_collected' => true,
                    'payment_collected_at' => now(),
                ]);
            }

            // Record payment if not already recorded
            if ($paymentIntent && !Payment::where('gateway_transaction_id', $paymentIntent)->exists()) {
                Payment::create([
                    'order_id' => $order->id,
                    'transaction_id' => $paymentIntent,
                    'gateway' => 'stripe',
                    'gateway_transaction_id' => $paymentIntent,
                    'method' => 'card',
                    'amount' => (float) $amountTotal,
                    'currency' => $currency,
                    'status' => 'captured',
                    'gateway_response' => $session,
                    'captured_at' => now(),
                ]);
            }

            $customerName = $order->shipping_name
                ?? trim(($order->customer?->first_name ?? '') . ' ' . ($order->customer?->last_name ?? ''))
                ?? 'Unknown';
            $orderNumber = $order->order_number;

            $orderItems = [];
            foreach ($order->items as $item) {
                $orderItems[] = ($item->product_name ?? $item->product?->name ?? 'Item') .
                    ' x' . $item->quantity .
                    ' (£' . number_format($item->total, 2) . ')';
            }

            $itemsList = $orderItems ? "\nItems: " . implode(', ', $orderItems) : '';
            $this->notifyAdmin(
                'Stripe Payment — £' . number_format($amountTotal, 2),
                "Order #{$orderNumber} — {$customerName} ({$customerEmail})" .
                "\nPayment Intent: {$paymentIntent}" .
                $itemsList,
                'payment',
                [
                    'stripe_session_id' => $sessionId,
                    'payment_intent' => $paymentIntent,
                    'amount' => $amountTotal,
                    'currency' => $currency,
                    'order_id' => $order->id,
                    'order_number' => $orderNumber,
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                ]
            );
        }

        Log::channel('stripe')->info("Checkout Session Completed", [
            'session_id' => $sessionId,
            'payment_intent' => $paymentIntent,
            'amount' => "{$currency} {$amountTotal}",
            'order_number' => $order?->order_number ?? 'N/A',
            'email' => $customerEmail,
        ]);

        return response()->json(['status' => 'ok', 'event' => 'session_completed']);
    }

    /**
     * payment_intent.succeeded - direct payment intent success.
     */
    private function handlePaymentSucceeded(array $intent)
    {
        $paymentIntentId = $intent['id'] ?? null;
        $amount = ($intent['amount'] ?? 0) / 100;
        $currency = strtoupper($intent['currency'] ?? 'GBP');

        Log::channel('stripe')->info("Payment Intent Succeeded", [
            'payment_intent' => $paymentIntentId,
            'amount' => "{$currency} {$amount}",
        ]);

        return response()->json(['status' => 'ok', 'event' => 'payment_succeeded']);
    }

    /**
     * payment_intent.payment_failed - payment failed.
     */
    private function handlePaymentFailed(array $intent)
    {
        $paymentIntentId = $intent['id'] ?? null;
        $amount = ($intent['amount'] ?? 0) / 100;
        $error = $intent['last_payment_error']['message'] ?? 'Unknown error';

        // Try to find the order via session
        $metadata = $intent['metadata'] ?? [];
        $reference = $metadata['reference'] ?? '';

        $this->notifyAdmin(
            'Stripe Payment Failed — £' . number_format($amount, 2),
            "Payment Intent: {$paymentIntentId}\nError: {$error}\nReference: {$reference}",
            'payment',
            [
                'payment_intent' => $paymentIntentId,
                'amount' => $amount,
                'error' => $error,
                'status' => 'failed',
            ]
        );

        Log::channel('stripe')->warning("Payment Failed", [
            'payment_intent' => $paymentIntentId,
            'amount' => $amount,
            'error' => $error,
        ]);

        return response()->json(['status' => 'ok', 'event' => 'payment_failed']);
    }

    /**
     * charge.refunded - refund processed.
     */
    private function handleRefund(array $charge)
    {
        $chargeId = $charge['id'] ?? null;
        $amountRefunded = ($charge['amount_refunded'] ?? 0) / 100;
        $currency = strtoupper($charge['currency'] ?? 'GBP');
        $paymentIntent = $charge['payment_intent'] ?? null;

        $this->notifyAdmin(
            'Stripe Refund — £' . number_format($amountRefunded, 2),
            "Charge: {$chargeId}\nPayment Intent: {$paymentIntent}\nRefund: £" . number_format($amountRefunded, 2),
            'payment',
            ['charge_id' => $chargeId, 'amount_refunded' => $amountRefunded, 'currency' => $currency]
        );

        Log::channel('stripe')->info("Charge Refunded", [
            'charge_id' => $chargeId,
            'amount_refunded' => "{$currency} {$amountRefunded}",
            'payment_intent' => $paymentIntent,
        ]);

        return response()->json(['status' => 'ok', 'event' => 'refunded']);
    }

    /**
     * Unhandled event type.
     */
    private function handleUnknown(string $eventType)
    {
        Log::channel('stripe')->info("Unhandled event: {$eventType}");
        return response()->json(['status' => 'ok', 'event' => 'unhandled']);
    }

    /**
     * Send an in-app notification to all admin users.
     */
    private function notifyAdmin(string $title, string $content, string $type = 'payment', array $data = []): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => $type,
                    'title' => $title,
                    'content' => $content,
                    'data' => $data,
                    'channel' => 'database',
                ]);
            }
        } catch (\Throwable $e) {
            Log::channel('stripe')->error("Failed to create admin notification: {$e->getMessage()}");
        }
    }
}
