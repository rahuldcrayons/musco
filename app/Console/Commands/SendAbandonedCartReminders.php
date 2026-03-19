<?php

namespace App\Console\Commands;

use App\Models\AbandonedCheckout;
use App\Models\Coupon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartReminders extends Command
{
    protected $signature = 'cart:remind-abandoned';
    protected $description = 'Send email and WhatsApp reminders for abandoned carts (30 min old)';

    public function handle(): int
    {
        $abandoned = AbandonedCheckout::where('recovered', false)
            ->whereNull('notified_at')
            ->where('created_at', '<=', now()->subMinutes(30))
            ->where('created_at', '>=', now()->subHours(24))
            ->whereNotNull('email')
            ->limit(50)
            ->get();

        $this->info("Found {$abandoned->count()} abandoned carts to notify");

        foreach ($abandoned as $checkout) {
            try {
                $discountCode = 'COMEBACK' . strtoupper(substr(md5($checkout->id . time()), 0, 4));
                Coupon::create([
                    'code' => $discountCode,
                    'name' => 'Abandoned Cart Recovery',
                    'type' => 'percentage',
                    'value' => 5,
                    'min_order_amount' => 0,
                    'usage_limit' => 1,
                    'usage_per_user' => 1,
                    'is_active' => true,
                    'expires_at' => now()->addHour(),
                ]);

                $cartUrl = url("/cart?recovery={$checkout->id}&code={$discountCode}");
                $customerName = $checkout->name ?? 'there';

                if ($checkout->email) {
                    Mail::send('emails.abandoned-cart', [
                        'name' => $customerName,
                        'cartUrl' => $cartUrl,
                        'discountCode' => $discountCode,
                        'cartTotal' => $checkout->cart_total,
                    ], function ($m) use ($checkout) {
                        $m->to($checkout->email)
                          ->from('jikra@jikra.in', 'Jikra')
                          ->subject('You left something behind! Here is 5% off just for you');
                    });
                }

                if ($checkout->phone) {
                    $token = config('services.meta.page_access_token');
                    $phoneId = config('services.meta.whatsapp_phone_number_id');
                    if ($token && $phoneId) {
                        $cleanPhone = preg_replace('/\D/', '', $checkout->phone);
                        if (!str_starts_with($cleanPhone, '91') && strlen($cleanPhone) === 10) {
                            $cleanPhone = '91' . $cleanPhone;
                        }
                        Http::withToken($token)->post("https://graph.facebook.com/v25.0/{$phoneId}/messages", [
                            'messaging_product' => 'whatsapp',
                            'to' => $cleanPhone,
                            'type' => 'text',
                            'text' => [
                                'body' => "Hi {$customerName}! You left items in your Jikra cart.\n\nUse code *{$discountCode}* for 5% OFF (valid for 1 hour only!)\n\nComplete your order: {$cartUrl}\n\nHurry, your cart is waiting!"
                            ],
                        ]);
                    }
                }

                $checkout->update(['notified_at' => now()]);
                $this->info("Notified: {$checkout->email}");

            } catch (\Exception $e) {
                Log::warning('Abandoned cart reminder failed', ['id' => $checkout->id, 'error' => $e->getMessage()]);
                $this->error("Failed: {$checkout->email} - {$e->getMessage()}");
            }
        }

        return 0;
    }
}
