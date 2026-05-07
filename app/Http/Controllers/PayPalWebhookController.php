<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    /**
     * Handle incoming PayPal webhook events.
     *
     * Webhook ID: 2KU633335N671394E
     * URL: https://trendymus.com/webhook/payment-updates
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['event_type'] ?? 'UNKNOWN';
        $resource = $payload['resource'] ?? [];

        Log::channel('paypal')->info("PayPal Webhook: {$eventType}", [
            'id' => $payload['id'] ?? null,
            'resource_type' => $payload['resource_type'] ?? null,
        ]);

        return match ($eventType) {
            'CHECKOUT.ORDER.APPROVED' => $this->handleOrderApproved($resource, $payload),
            'PAYMENT.CAPTURE.COMPLETED' => $this->handleCaptureCompleted($resource, $payload),
            'PAYMENT.CAPTURE.DENIED' => $this->handleCaptureDenied($resource, $payload),
            'PAYMENT.CAPTURE.REFUNDED' => $this->handleCaptureRefunded($resource, $payload),
            'CHECKOUT.ORDER.COMPLETED' => $this->handleOrderCompleted($resource, $payload),
            default => $this->handleUnknown($eventType, $payload),
        };
    }

    /**
     * CHECKOUT.ORDER.APPROVED — buyer approved the payment on PayPal.
     */
    private function handleOrderApproved(array $resource, array $payload)
    {
        $paypalOrderId = $resource['id'] ?? null;
        $payer = $resource['payer'] ?? [];
        $purchaseUnits = $resource['purchase_units'] ?? [];

        $customerName = trim(($payer['name']['given_name'] ?? '') . ' ' . ($payer['name']['surname'] ?? ''));
        $customerEmail = $payer['email_address'] ?? 'N/A';
        $amount = $purchaseUnits[0]['amount']['value'] ?? '0.00';
        $currency = $purchaseUnits[0]['amount']['currency_code'] ?? 'GBP';

        $order = $paypalOrderId ? Order::where('paypal_order_id', $paypalOrderId)->first() : null;

        $this->notifyAdmin(
            'Payment Approved',
            ($order ? "Order #{$order->order_number}" : 'New payment') .
            " — {$customerName} ({$customerEmail}) approved £{$amount} via PayPal.",
            'payment',
            [
                'paypal_order_id' => $paypalOrderId,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'amount' => $amount,
                'currency' => $currency,
                'order_id' => $order?->id,
                'order_number' => $order?->order_number,
            ]
        );

        Log::channel('paypal')->info("Order Approved", [
            'paypal_order_id' => $paypalOrderId,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'amount' => "{$currency} {$amount}",
            'our_order' => $order?->order_number,
        ]);

        return response()->json(['status' => 'ok', 'event' => 'order_approved']);
    }

    /**
     * PAYMENT.CAPTURE.COMPLETED — payment successfully captured.
     */
    private function handleCaptureCompleted(array $resource, array $payload)
    {
        $captureId = $resource['id'] ?? null;
        $amount = $resource['amount']['value'] ?? '0.00';
        $currency = $resource['amount']['currency_code'] ?? 'GBP';
        $status = $resource['status'] ?? 'UNKNOWN';

        $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        // Find our order
        $order = null;
        if ($paypalOrderId) {
            $order = Order::where('paypal_order_id', $paypalOrderId)->first();
        }
        if (!$order && $captureId) {
            $payment = Payment::where('gateway_transaction_id', $captureId)
                ->orWhere('transaction_id', $captureId)
                ->first();
            if ($payment) {
                $order = $payment->order;
            }
        }

        $customerName = 'Unknown';
        $customerEmail = 'N/A';
        $orderNumber = 'N/A';
        $orderItems = [];

        if ($order) {
            $order->load(['items.product', 'customer']);

            $customerName = $order->shipping_name
                ?? trim(($order->customer?->first_name ?? '') . ' ' . ($order->customer?->last_name ?? ''))
                ?? 'Unknown';
            $customerEmail = $order->email
                ?? $order->customer?->email
                ?? 'N/A';
            $orderNumber = $order->order_number;

            foreach ($order->items as $item) {
                $orderItems[] = ($item->product_name ?? $item->product?->name ?? 'Item') .
                    ' x' . $item->quantity .
                    ' (£' . number_format($item->total, 2) . ')';
            }

            // Update payment status
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_collected' => true,
                    'payment_collected_at' => now(),
                ]);
            }

            // Record payment if not already recorded
            if (!Payment::where('gateway_transaction_id', $captureId)->exists()) {
                Payment::create([
                    'order_id' => $order->id,
                    'transaction_id' => $captureId,
                    'gateway' => 'paypal',
                    'gateway_transaction_id' => $captureId,
                    'method' => 'paypal',
                    'amount' => (float) $amount,
                    'currency' => $currency,
                    'status' => 'captured',
                    'gateway_response' => $resource,
                    'captured_at' => now(),
                ]);
            }
        }

        // Notify admin
        $itemsList = $orderItems ? "\nItems: " . implode(', ', $orderItems) : '';
        $this->notifyAdmin(
            'Payment Captured — £' . $amount,
            "Order #{$orderNumber} — {$customerName} ({$customerEmail})" .
            "\nPayPal Transaction: {$captureId}" .
            $itemsList,
            'payment',
            [
                'capture_id' => $captureId,
                'paypal_order_id' => $paypalOrderId,
                'amount' => $amount,
                'currency' => $currency,
                'order_id' => $order?->id,
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'items' => $orderItems,
            ]
        );

        Log::channel('paypal')->info("Payment Captured", [
            'capture_id' => $captureId,
            'paypal_order_id' => $paypalOrderId,
            'amount' => "{$currency} {$amount}",
            'order_number' => $orderNumber,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'items' => $orderItems,
        ]);

        return response()->json(['status' => 'ok', 'event' => 'capture_completed']);
    }

    /**
     * PAYMENT.CAPTURE.DENIED — payment was denied.
     */
    private function handleCaptureDenied(array $resource, array $payload)
    {
        $captureId = $resource['id'] ?? null;
        $amount = $resource['amount']['value'] ?? '0.00';
        $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        $order = $paypalOrderId ? Order::where('paypal_order_id', $paypalOrderId)->first() : null;

        if ($order && $order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'failed']);
        }

        $this->notifyAdmin(
            'Payment Denied — £' . $amount,
            ($order ? "Order #{$order->order_number}" : 'Unknown order') .
            " — Payment was denied by PayPal.\nCapture ID: {$captureId}",
            'payment',
            [
                'capture_id' => $captureId,
                'paypal_order_id' => $paypalOrderId,
                'amount' => $amount,
                'order_id' => $order?->id,
                'order_number' => $order?->order_number,
                'status' => 'denied',
            ]
        );

        Log::channel('paypal')->warning("Payment Denied", [
            'capture_id' => $captureId,
            'paypal_order_id' => $paypalOrderId,
            'amount' => $amount,
            'order_number' => $order?->order_number ?? 'N/A',
        ]);

        return response()->json(['status' => 'ok', 'event' => 'capture_denied']);
    }

    /**
     * PAYMENT.CAPTURE.REFUNDED — a refund was issued.
     */
    private function handleCaptureRefunded(array $resource, array $payload)
    {
        $refundId = $resource['id'] ?? null;
        $amount = $resource['amount']['value'] ?? '0.00';
        $currency = $resource['amount']['currency_code'] ?? 'GBP';

        $this->notifyAdmin(
            'Refund Processed — £' . $amount,
            "PayPal refund {$refundId} for £{$amount} has been processed.",
            'payment',
            ['refund_id' => $refundId, 'amount' => $amount, 'currency' => $currency]
        );

        Log::channel('paypal')->info("Payment Refunded", [
            'refund_id' => $refundId,
            'amount' => "{$currency} {$amount}",
        ]);

        return response()->json(['status' => 'ok', 'event' => 'capture_refunded']);
    }

    /**
     * CHECKOUT.ORDER.COMPLETED — entire checkout completed.
     */
    private function handleOrderCompleted(array $resource, array $payload)
    {
        $paypalOrderId = $resource['id'] ?? null;
        $order = $paypalOrderId ? Order::where('paypal_order_id', $paypalOrderId)->first() : null;

        Log::channel('paypal')->info("Checkout Completed", [
            'paypal_order_id' => $paypalOrderId,
            'order_number' => $order?->order_number ?? 'N/A',
        ]);

        return response()->json(['status' => 'ok', 'event' => 'order_completed']);
    }

    /**
     * Unhandled event type.
     */
    private function handleUnknown(string $eventType, array $payload)
    {
        Log::channel('paypal')->info("Unhandled event: {$eventType}");
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
            Log::channel('paypal')->error("Failed to create admin notification: {$e->getMessage()}");
        }
    }
}
