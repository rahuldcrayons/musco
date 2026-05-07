<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PaymentConfirmation;
use App\Mail\PaymentFailed as PaymentFailedMail;
use App\Models\Order;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * POST /api/webhook/razorpay
     * Handle all Razorpay webhook events.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event = $payload['event'] ?? 'unknown';

        Log::info('Razorpay webhook received', ['event' => $event]);

        match ($event) {
            'payment.authorized' => $this->handlePaymentAuthorized($payload),
            'payment.captured' => $this->handlePaymentCaptured($payload),
            'payment.failed' => $this->handlePaymentFailed($payload),
            'order.paid' => $this->handleOrderPaid($payload),
            'refund.processed', 'refund.created' => $this->handleRefundProcessed($payload),
            default => Log::info('Razorpay: Unhandled event', ['event' => $event]),
        };

        return response()->json(['status' => 'ok']);
    }

    private function handlePaymentAuthorized(array $payload): void
    {
        $entity = $payload['payload']['payment']['entity'] ?? [];
        $orderId = $entity['notes']['order_id'] ?? null;
        $razorpayPaymentId = $entity['id'] ?? null;

        if (!$orderId || !$razorpayPaymentId) {
            Log::warning('Razorpay: Missing order_id or payment_id in payment.authorized', $entity);
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            Log::warning('Razorpay: Order not found', ['order_id' => $orderId]);
            return;
        }

        // Create or update payment record
        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id, 'gateway' => 'razorpay'],
            [
                'gateway_transaction_id' => $razorpayPaymentId,
                'method' => $entity['method'] ?? 'unknown',
                'amount' => ($entity['amount'] ?? 0) / 100, // Razorpay sends in paise
                'currency' => $entity['currency'] ?? 'GBP',
                'status' => 'authorized',
                'gateway_response' => $entity,
                'ip_address' => $entity['ip'] ?? null,
                'authorized_at' => now(),
            ]
        );

        $order->update(['payment_status' => 'authorized']);

        Log::info('Razorpay: Payment authorized', [
            'order_id' => $order->id,
            'payment_id' => $razorpayPaymentId,
        ]);

        // Send notifications
        $this->sendPaymentNotification($order, $payment, 'authorized');
    }

    private function handlePaymentCaptured(array $payload): void
    {
        $entity = $payload['payload']['payment']['entity'] ?? [];
        $orderId = $entity['notes']['order_id'] ?? null;
        $razorpayPaymentId = $entity['id'] ?? null;

        if (!$orderId || !$razorpayPaymentId) {
            Log::warning('Razorpay: Missing order_id or payment_id in payment.captured', $entity);
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            Log::warning('Razorpay: Order not found', ['order_id' => $orderId]);
            return;
        }

        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id, 'gateway' => 'razorpay'],
            [
                'gateway_transaction_id' => $razorpayPaymentId,
                'method' => $entity['method'] ?? 'unknown',
                'amount' => ($entity['amount'] ?? 0) / 100,
                'currency' => $entity['currency'] ?? 'GBP',
                'status' => 'captured',
                'gateway_response' => $entity,
                'captured_at' => now(),
            ]
        );

        $order->update([
            'payment_status' => 'paid',
            'paid_amount' => ($entity['amount'] ?? 0) / 100,
        ]);

        Log::info('Razorpay: Payment captured', [
            'order_id' => $order->id,
            'payment_id' => $razorpayPaymentId,
        ]);

        // Send notifications
        $this->sendPaymentNotification($order, $payment, 'captured');
    }

    private function handlePaymentFailed(array $payload): void
    {
        $entity = $payload['payload']['payment']['entity'] ?? [];
        $orderId = $entity['notes']['order_id'] ?? null;
        $razorpayPaymentId = $entity['id'] ?? null;

        if (!$orderId) {
            Log::warning('Razorpay: Missing order_id in payment.failed', $entity);
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            return;
        }

        $errorDescription = $entity['error_description'] ?? $entity['error_reason'] ?? 'Payment failed';

        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id, 'gateway' => 'razorpay'],
            [
                'gateway_transaction_id' => $razorpayPaymentId,
                'method' => $entity['method'] ?? 'unknown',
                'amount' => ($entity['amount'] ?? 0) / 100,
                'currency' => $entity['currency'] ?? 'GBP',
                'status' => 'failed',
                'failure_reason' => $errorDescription,
                'gateway_response' => $entity,
            ]
        );

        $order->update(['payment_status' => 'failed']);

        Log::info('Razorpay: Payment failed', [
            'order_id' => $order->id,
            'reason' => $errorDescription,
        ]);

        // Send failure notifications
        $this->sendPaymentNotification($order, $payment, 'failed');
    }

    private function handleOrderPaid(array $payload): void
    {
        $entity = $payload['payload']['order']['entity'] ?? [];
        $razorpayOrderId = $entity['id'] ?? null;
        $notes = $entity['notes'] ?? [];
        $orderId = $notes['order_id'] ?? null;

        if (!$orderId) {
            Log::info('Razorpay: order.paid received without order_id in notes', $entity);
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            return;
        }

        // Mark as paid if not already
        if ($order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'paid_amount' => ($entity['amount_paid'] ?? 0) / 100,
            ]);
        }

        Log::info('Razorpay: Order paid', ['order_id' => $order->id]);
    }

    private function handleRefundProcessed(array $payload): void
    {
        $entity = $payload['payload']['refund']['entity'] ?? [];
        $paymentId = $entity['payment_id'] ?? null;

        if (!$paymentId) {
            return;
        }

        $payment = Payment::where('gateway_transaction_id', $paymentId)
            ->where('gateway', 'razorpay')
            ->first();

        if (!$payment) {
            Log::warning('Razorpay: Payment not found for refund', ['payment_id' => $paymentId]);
            return;
        }

        $refundAmount = ($entity['amount'] ?? 0) / 100;

        $payment->refunds()->create([
            'order_id' => $payment->order_id,
            'amount' => $refundAmount,
            'reason' => $entity['notes']['reason'] ?? 'Refund processed via Razorpay',
            'status' => 'processed',
            'gateway_refund_id' => $entity['id'] ?? null,
        ]);

        // Update payment status if fully refunded
        if ($refundAmount >= $payment->amount) {
            $payment->update(['status' => 'refunded']);
            $payment->order->update(['payment_status' => 'refunded']);
        }

        Log::info('Razorpay: Refund processed', [
            'payment_id' => $paymentId,
            'amount' => $refundAmount,
        ]);
    }

    /**
     * Send email, in-app, and WhatsApp notifications for payment events.
     */
    private function sendPaymentNotification(Order $order, Payment $payment, string $status): void
    {
        $user = $order->user;

        // Determine recipient details (registered user or guest)
        $email = $user?->email ?? $order->guest_email;
        $phone = $user?->phone ?? $order->guest_phone;
        $name = $user?->first_name ?? $order->guest_name ?? 'Customer';

        // --- Email notification ---
        if ($email) {
            try {
                $mailable = match ($status) {
                    'authorized', 'captured' => new PaymentConfirmation($order, $payment),
                    'failed' => new PaymentFailedMail($order, $payment),
                    default => null,
                };

                if ($mailable) {
                    \Illuminate\Support\Facades\Mail::to($email)->queue($mailable);
                }
            } catch (\Throwable $e) {
                Log::error('Razorpay: Failed to send payment email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // --- In-app notification (for registered users) ---
        if ($user) {
            $notifData = match ($status) {
                'authorized', 'captured' => [
                    'title' => 'Payment Successful',
                    'content' => "Payment of " . format_price($payment->amount) . " received for order #{$order->order_number}.",
                ],
                'failed' => [
                    'title' => 'Payment Failed',
                    'content' => "Payment for order #{$order->order_number} failed. Please retry.",
                ],
                default => null,
            };

            if ($notifData) {
                $this->notificationService->notifyInApp(
                    $user,
                    "payment_{$status}",
                    $notifData['title'],
                    $notifData['content'],
                    ['order_id' => $order->id, 'payment_id' => $payment->id]
                );
            }
        }

        // --- WhatsApp notification ---
        if ($phone) {
            $this->sendWhatsAppPaymentNotification($phone, $name, $order, $payment, $status);
        }
    }

    /**
     * Send WhatsApp message for payment status via Meta Cloud API.
     */
    private function sendWhatsAppPaymentNotification(
        string $phone,
        string $name,
        Order $order,
        Payment $payment,
        string $status
    ): void {
        $token = config('services.meta.page_access_token');
        $phoneNumberId = config('services.meta.whatsapp_phone_number_id');

        if (empty($token) || empty($phoneNumberId)) {
            return;
        }

        $message = match ($status) {
            'authorized', 'captured' => "Hi {$name}! ✅ Payment of £{$payment->amount} received for your Trendymus order #{$order->order_number}. Thank you for shopping with us! Track your order at: " . url("/orders/{$order->id}"),
            'failed' => "Hi {$name}, ❌ Payment for your Trendymus order #{$order->order_number} failed. Please retry at: " . url("/orders/{$order->id}") . " or contact our support team for help.",
            default => null,
        };

        if (!$message) {
            return;
        }

        // Clean phone number (remove non-digits, ensure country code)
        $cleanPhone = preg_replace('/\D/', '', $phone);
        if (!str_starts_with($cleanPhone, '91') && strlen($cleanPhone) === 10) {
            $cleanPhone = '91' . $cleanPhone;
        }

        try {
            Http::withToken($token)
                ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $cleanPhone,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);
        } catch (\Throwable $e) {
            Log::error('Razorpay: WhatsApp payment notification failed', [
                'phone' => $cleanPhone,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
