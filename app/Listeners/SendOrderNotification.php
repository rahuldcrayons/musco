<?php

namespace App\Listeners;

use App\Events\OrderDelivered;
use App\Events\OrderPlaced;
use App\Events\OrderShipped;
use App\Events\OrderStatusChanged;
use App\Events\RefundProcessed;
use App\Events\ReturnRequested;
use App\Mail\AdminNewOrder;
use App\Mail\OrderConfirmation;
use App\Mail\OrderDelivered as OrderDeliveredMail;
use App\Mail\OrderShipped as OrderShippedMail;
use App\Mail\RefundProcessed as RefundProcessedMail;
use App\Mail\ReturnApproved;
use App\Models\Order;
use App\Models\Setting;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderNotification
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function handleOrderPlaced(OrderPlaced $event): void
    {
        $order = $event->order;
        $user = $order->user;

        // Email + in-app for registered users
        if ($user) {
            $this->notificationService->notify($user, 'order_placed', [
                'title' => 'Order Confirmed',
                'content' => "Your order #{$order->order_number} has been confirmed.",
                'order_id' => $order->id,
            ], new OrderConfirmation($order));
        } else {
            // Guest order — send confirmation email directly
            $guestEmail = $order->guest_email;
            if ($guestEmail) {
                try {
                    Mail::to($guestEmail)->queue(new OrderConfirmation($order));
                } catch (\Exception $e) {
                    Log::error('Failed to send guest order email', ['email' => $guestEmail, 'error' => $e->getMessage()]);
                }
            }
        }

        // WhatsApp to customer
        $customerPhone = $user?->phone ?? $order->guest_phone;
        $customerName = $user?->first_name ?? $order->guest_name ?? 'Customer';
        if ($customerPhone) {
            $this->sendWhatsApp(
                $customerPhone,
                "Hi {$customerName}! 🎉 Your Trendymus order #{$order->order_number} is confirmed!\n\n"
                . "Order Total: £" . number_format($order->total, 2) . "\n"
                . "Payment: " . ($order->payment_status === 'paid' ? '✅ Paid' : '⏳ Pending') . "\n\n"
                . "Track your order: " . url("/track-order") . "\n\n"
                . "Thank you for shopping with Trendymus! 🛍️"
            );
        }

        // WhatsApp to admin
        $adminPhone = Setting::get('admin_whatsapp_phone', '447354567705');
        if ($adminPhone) {
            $itemsSummary = $order->items->map(fn($item) => "• {$item->product_name} x{$item->quantity} — £" . number_format($item->total, 2))->implode("\n");
            $address = $order->shipping_address_snapshot ?? [];
            $addressLine = ($address['address_line_1'] ?? '') . ', ' . ($address['city'] ?? '') . ' ' . ($address['postal_code'] ?? '');

            $this->sendWhatsApp(
                $adminPhone,
                "🔔 *NEW ORDER* #{$order->order_number}\n\n"
                . "Customer: {$customerName}\n"
                . "Phone: {$customerPhone}\n"
                . "Payment: " . ($order->payment_status === 'paid' ? 'Prepaid ✅' : 'Pending ⏳') . "\n\n"
                . "Items:\n{$itemsSummary}\n\n"
                . "Total: *£" . number_format($order->total, 2) . "*\n"
                . "Ship to: {$addressLine}\n\n"
                . "Manage: " . url("/admin/orders/{$order->id}")
            );
        }

        // Email to admin
        $adminEmail = Setting::get('admin_email', 'info@trendymus.co.uk');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->queue(new AdminNewOrder($order));
            } catch (\Exception $e) {
                Log::error('Failed to send admin order email', ['error' => $e->getMessage()]);
            }
        }
    }

    public function handleOrderShipped(OrderShipped $event): void
    {
        $order = $event->order;
        $user = $order->user;

        if ($user) {
            $this->notificationService->notify($user, 'order_shipped', [
                'title' => 'Order Shipped',
                'content' => "Your order #{$order->order_number} has been shipped.",
                'order_id' => $order->id,
                'tracking_number' => $event->trackingNumber,
            ], new OrderShippedMail($order, $event->trackingNumber));
        }

        // WhatsApp to customer
        $customerPhone = $user?->phone ?? $order->guest_phone;
        $customerName = $user?->first_name ?? $order->guest_name ?? 'Customer';
        if ($customerPhone) {
            $trackingInfo = $event->trackingNumber ? "\nTracking: {$event->trackingNumber}" : '';
            $this->sendWhatsApp(
                $customerPhone,
                "Hi {$customerName}! 📦 Your Trendymus order #{$order->order_number} has been shipped!{$trackingInfo}\n\n"
                . "Track: " . url("/track-order") . "\n\n"
                . "Thank you for shopping with Trendymus!"
            );
        }
    }

    public function handleOrderDelivered(OrderDelivered $event): void
    {
        $order = $event->order;
        $user = $order->user;

        if ($user) {
            $this->notificationService->notify($user, 'order_delivered', [
                'title' => 'Order Delivered',
                'content' => "Your order #{$order->order_number} has been delivered.",
                'order_id' => $order->id,
            ], new OrderDeliveredMail($order));
        }

        // WhatsApp to customer
        $customerPhone = $user?->phone ?? $order->guest_phone;
        $customerName = $user?->first_name ?? $order->guest_name ?? 'Customer';
        if ($customerPhone) {
            $this->sendWhatsApp(
                $customerPhone,
                "Hi {$customerName}! ✅ Your Trendymus order #{$order->order_number} has been delivered!\n\n"
                . "We hope you love your purchase. If you need any help, reply here or visit " . url("/track-order") . "\n\n"
                . "Thank you for choosing Trendymus! 💚"
            );
        }
    }

    public function handleOrderStatusChanged(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $user = $order->user;
        if (! $user) {
            return;
        }

        if ($event->newStatus === 'cancelled') {
            $this->notificationService->notifyInApp($user, 'order_cancelled',
                'Order Cancelled',
                "Your order #{$order->order_number} has been cancelled.",
                ['order_id' => $order->id]
            );
        }
    }

    public function handleReturnRequested(ReturnRequested $event): void
    {
        $return = $event->return;
        $user = $return->order?->user;
        if (! $user) {
            return;
        }

        if ($return->status === 'approved') {
            $this->notificationService->notify($user, 'return_approved', [
                'title' => 'Return Approved',
                'content' => "Your return request #{$return->return_number} has been approved.",
                'return_id' => $return->id,
            ], new ReturnApproved($return));
        } else {
            $this->notificationService->notifyInApp($user, 'return_' . $return->status,
                'Return Update',
                "Your return request #{$return->return_number} status: {$return->status}.",
                ['return_id' => $return->id]
            );
        }
    }

    public function handleRefundProcessed(RefundProcessed $event): void
    {
        $return = $event->return;
        $user = $return->order?->user;
        if (! $user) {
            return;
        }

        $this->notificationService->notify($user, 'refund_processed', [
            'title' => 'Refund Processed',
            'content' => 'Your refund of ' . format_price($event->amount) . ' has been processed.',
            'return_id' => $return->id,
            'amount' => $event->amount,
        ], new RefundProcessedMail($return, $event->amount));
    }

    /**
     * Send WhatsApp message via Meta Cloud API.
     */
    private function sendWhatsApp(string $phone, string $message): void
    {
        $token = config('services.meta.page_access_token');
        $phoneNumberId = config('services.meta.whatsapp_phone_number_id');

        if (empty($token) || empty($phoneNumberId)) {
            return;
        }

        // Clean phone: ensure 44 prefix for UK numbers
        $cleanPhone = preg_replace('/\D/', '', $phone);
        if (!str_starts_with($cleanPhone, '44') && strlen($cleanPhone) >= 10 && strlen($cleanPhone) <= 11) {
            $cleanPhone = '44' . ltrim($cleanPhone, '0');
        }

        try {
            $response = Http::withToken($token)
                ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $cleanPhone,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

            if ($response->failed()) {
                Log::warning('WhatsApp order notification failed', [
                    'phone' => $cleanPhone,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp order notification error', [
                'phone' => $cleanPhone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
