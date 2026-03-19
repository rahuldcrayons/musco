<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShiprocketService
{
    private string $baseUrl = 'https://apiv2.shiprocket.in/v1/external';
    private ?string $token = null;

    public function __construct()
    {
        $this->token = $this->getToken();
    }

    /**
     * Authenticate and get/cache Shiprocket token.
     */
    private function getToken(): ?string
    {
        return Cache::remember('shiprocket_token', 86000, function () {
            $email = Setting::get('shiprocket_email', config('services.shiprocket.email'));
            $password = Setting::get('shiprocket_password', config('services.shiprocket.password'));

            if (empty($email) || empty($password)) {
                return null;
            }

            try {
                $response = Http::post("{$this->baseUrl}/auth/login", [
                    'email' => $email,
                    'password' => $password,
                ]);

                if ($response->successful()) {
                    return $response->json('token');
                }
            } catch (\Exception $e) {
                Log::error('Shiprocket auth failed: ' . $e->getMessage());
            }

            return null;
        });
    }

    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    /**
     * Check serviceability and get shipping rates for a pincode.
     */
    public function checkServiceability(string $pickupPincode, string $deliveryPincode, float $weight = 0.5, ?float $codAmount = null): array
    {
        if (!$this->token) {
            return ['available' => false, 'message' => 'Shiprocket not configured'];
        }

        try {
            $params = [
                'pickup_postcode' => $pickupPincode,
                'delivery_postcode' => $deliveryPincode,
                'weight' => $weight,
                'cod' => $codAmount ? 1 : 0,
            ];

            $response = $this->api('GET', '/courier/serviceability/', $params);

            if (!$response->successful()) {
                return ['available' => false, 'message' => 'Service check failed'];
            }

            $data = $response->json('data.available_courier_companies', []);

            if (empty($data)) {
                return ['available' => false, 'message' => 'Delivery not available to this pincode'];
            }

            // Get cheapest and fastest options
            $cheapest = collect($data)->sortBy('rate')->first();
            $fastest = collect($data)->sortBy('estimated_delivery_days')->first();

            return [
                'available' => true,
                'couriers' => count($data),
                'cheapest' => [
                    'name' => $cheapest['courier_name'] ?? '',
                    'rate' => $cheapest['rate'] ?? 0,
                    'etd' => $cheapest['etd'] ?? '',
                    'estimated_days' => $cheapest['estimated_delivery_days'] ?? 5,
                ],
                'fastest' => [
                    'name' => $fastest['courier_name'] ?? '',
                    'rate' => $fastest['rate'] ?? 0,
                    'etd' => $fastest['etd'] ?? '',
                    'estimated_days' => $fastest['estimated_delivery_days'] ?? 3,
                ],
                'all' => collect($data)->map(fn ($c) => [
                    'id' => $c['courier_company_id'] ?? 0,
                    'name' => $c['courier_name'] ?? '',
                    'rate' => $c['rate'] ?? 0,
                    'etd' => $c['etd'] ?? '',
                    'estimated_days' => $c['estimated_delivery_days'] ?? 5,
                    'cod' => $c['cod'] ?? 0,
                ])->toArray(),
            ];
        } catch (\Exception $e) {
            Log::error('Shiprocket serviceability check failed: ' . $e->getMessage());
            return ['available' => false, 'message' => 'Service check error'];
        }
    }

    /**
     * Create a Shiprocket order from our order.
     */
    public function createOrder(Order $order): array
    {
        if (!$this->token) {
            return ['success' => false, 'message' => 'Shiprocket not configured'];
        }

        try {
            $items = $order->items->map(fn ($item) => [
                'name' => $item->product_name,
                'sku' => $item->product?->sku ?? 'SKU-' . $item->product_id,
                'units' => $item->quantity,
                'selling_price' => (float) $item->price,
                'discount' => 0,
                'tax' => 0,
                'hsn' => '',
            ])->toArray();

            $address = $order->shippingAddress ?? $order->billingAddress;

            $payload = [
                'order_id' => $order->order_number,
                'order_date' => $order->created_at->format('Y-m-d H:i'),
                'pickup_location' => Setting::get('shiprocket_pickup_location', 'Primary'),
                'billing_customer_name' => $address->name ?? $order->customer_name,
                'billing_last_name' => '',
                'billing_address' => $address->address_line_1 ?? '',
                'billing_address_2' => $address->address_line_2 ?? '',
                'billing_city' => $address->city ?? '',
                'billing_pincode' => $address->postal_code ?? '',
                'billing_state' => $address->state ?? '',
                'billing_country' => 'India',
                'billing_email' => $order->email ?? '',
                'billing_phone' => $order->phone ?? '',
                'shipping_is_billing' => true,
                'order_items' => $items,
                'payment_method' => $order->payment_method === 'cod' ? 'COD' : 'Prepaid',
                'sub_total' => (float) $order->total,
                'length' => 20,
                'breadth' => 15,
                'height' => 10,
                'weight' => 0.5,
            ];

            $response = $this->api('POST', '/orders/create/adhoc', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'shiprocket_order_id' => $data['order_id'] ?? null,
                    'shipment_id' => $data['shipment_id'] ?? null,
                    'awb' => $data['awb_code'] ?? null,
                ];
            }

            return ['success' => false, 'message' => $response->json('message', 'Order creation failed')];
        } catch (\Exception $e) {
            Log::error('Shiprocket create order failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Track a shipment by AWB number.
     */
    public function trackShipment(string $awb): array
    {
        if (!$this->token) {
            return ['success' => false];
        }

        try {
            $response = $this->api('GET', "/courier/track/awb/{$awb}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json('tracking_data', []),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Shiprocket tracking failed: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Generate shipping label for a shipment.
     */
    public function generateLabel(int $shipmentId): ?string
    {
        if (!$this->token) {
            return null;
        }

        try {
            $response = $this->api('POST', '/courier/generate/label', [
                'shipment_id' => [$shipmentId],
            ]);

            if ($response->successful()) {
                return $response->json('label_url');
            }
        } catch (\Exception $e) {
            Log::error('Shiprocket label generation failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Make authenticated API call to Shiprocket.
     */
    private function api(string $method, string $endpoint, array $data = [])
    {
        $request = Http::withToken($this->token)->timeout(15);

        return match (strtoupper($method)) {
            'GET' => $request->get("{$this->baseUrl}{$endpoint}", $data),
            'POST' => $request->post("{$this->baseUrl}{$endpoint}", $data),
            'PUT' => $request->put("{$this->baseUrl}{$endpoint}", $data),
            default => $request->get("{$this->baseUrl}{$endpoint}", $data),
        };
    }
}
