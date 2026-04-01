<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verify webhook (GET) - Meta sends this to verify your endpoint.
     */
    public function verify(Request $request): Response
    {
        $verifyToken = config('services.whatsapp.verify_token', 'musco_whatsapp_verify_2026');
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('WhatsApp webhook verified successfully');
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming webhook events (POST).
     */
    public function handle(Request $request): Response
    {
        $payload = $request->all();

        Log::info('WhatsApp webhook received', ['payload' => $payload]);

        // Process messages
        $entries = $payload['entry'] ?? [];
        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                $value = $change['value'] ?? [];
                $messages = $value['messages'] ?? [];

                foreach ($messages as $message) {
                    $this->processMessage($message, $value);
                }

                // Handle status updates (sent, delivered, read)
                $statuses = $value['statuses'] ?? [];
                foreach ($statuses as $status) {
                    Log::info('WhatsApp message status', [
                        'id' => $status['id'] ?? '',
                        'status' => $status['status'] ?? '',
                        'recipient' => $status['recipient_id'] ?? '',
                    ]);
                }
            }
        }

        return response('OK', 200);
    }

    /**
     * Process an incoming WhatsApp message.
     */
    private function processMessage(array $message, array $value): void
    {
        $from = $message['from'] ?? '';
        $type = $message['type'] ?? '';
        $timestamp = $message['timestamp'] ?? '';
        $messageId = $message['id'] ?? '';

        // Get contact name
        $contacts = $value['contacts'] ?? [];
        $contactName = $contacts[0]['profile']['name'] ?? 'Unknown';

        Log::info('WhatsApp message received', [
            'from' => $from,
            'name' => $contactName,
            'type' => $type,
            'message_id' => $messageId,
        ]);

        // Handle text messages
        if ($type === 'text') {
            $text = $message['text']['body'] ?? '';
            Log::info("WhatsApp text from {$contactName} ({$from}): {$text}");

            // Auto-reply for common queries
            $this->autoReply($from, $text);
        }

        // Handle media (images, videos) — for video review cashback offer
        if (in_array($type, ['image', 'video'])) {
            $mediaId = $message[$type]['id'] ?? '';
            $caption = $message[$type]['caption'] ?? '';
            Log::info("WhatsApp {$type} from {$contactName} ({$from})", [
                'media_id' => $mediaId,
                'caption' => $caption,
            ]);
        }
    }

    /**
     * Send auto-reply based on message content.
     */
    private function autoReply(string $to, string $text): void
    {
        $textLower = strtolower(trim($text));

        $reply = null;

        if (in_array($textLower, ['hi', 'hello', 'hey', 'hii'])) {
            $reply = "Hello! 👋 Welcome to MusCo! 🛍️\n\nHow can we help you today?\n\n1️⃣ Track my order\n2️⃣ Browse products\n3️⃣ Video review cashback\n4️⃣ Talk to support\n\nReply with a number or type your query!";
        } elseif (str_contains($textLower, 'track') || str_contains($textLower, 'order')) {
            $reply = "📦 To track your order, please share your Order ID (e.g., ORD-20260318001).\n\nYou can also track at: https://musco.com/orders";
        } elseif (str_contains($textLower, 'video') || str_contains($textLower, 'cashback') || str_contains($textLower, '100')) {
            $reply = "🎥 *Video Review ₹100 Cashback Offer!*\n\n1. Record a 30-60 sec video of your MusCo product\n2. Send the video here on WhatsApp\n3. Get ₹100 cashback in your UPI within 48 hours!\n\nPlease send your video and your UPI ID. 💰";
        } elseif (str_contains($textLower, 'product') || str_contains($textLower, 'shop') || str_contains($textLower, 'buy')) {
            $reply = "🛍️ Browse our products at:\n👉 https://musco.com/products\n\n🔥 *Navratri Special: Extra 5% OFF at checkout!*\n\nAlso available on Amazon:\n👉 https://www.amazon.in/stores/MUSCO";
        }

        if ($reply) {
            $this->sendMessage($to, $reply);
        }
    }

    /**
     * Send a WhatsApp message.
     */
    private function sendMessage(string $to, string $text): void
    {
        $token = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_number_id');

        if (!$token || !$phoneId) {
            Log::warning('WhatsApp credentials not configured');
            return;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->post("https://graph.facebook.com/v21.0/{$phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => ['body' => $text],
                ]);

            if (!$response->successful()) {
                Log::error('WhatsApp send failed', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage());
        }
    }
}
