<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DelhiveryService
{
    private string $baseUrl = 'https://track.delhivery.com';
    private string $token;
    private string $pickupLocation;
    private string $originPin;

    public function __construct()
    {
        $this->token = Setting::get('delhivery_api_token', config('services.delhivery.token', ''));
        $this->pickupLocation = Setting::get('delhivery_pickup_location', 'Trendymus Warehouse');
        $this->originPin = Setting::get('delhivery_origin_pin', '110085');
    }

    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    // ─── Pincode Serviceability ───────────────────────────────

    public function checkPincode(string $pincode): array
    {
        return Cache::remember("delhivery_pin_{$pincode}", 86400, function () use ($pincode) {
            try {
                $response = $this->api('GET', '/c/api/pin-codes/json/', ['filter_codes' => $pincode]);

                if (!$response->successful()) {
                    return ['serviceable' => false, 'message' => 'Unable to check'];
                }

                $codes = $response->json('delivery_codes', []);
                if (empty($codes)) {
                    return ['serviceable' => false, 'message' => 'Pincode not serviceable'];
                }

                $pin = $codes[0]['postal_code'] ?? [];

                return [
                    'serviceable' => true,
                    'cod' => ($pin['cod'] ?? 'N') === 'Y',
                    'prepaid' => ($pin['pre_paid'] ?? 'N') === 'Y',
                    'pickup' => ($pin['pickup'] ?? 'N') === 'Y',
                    'city' => $pin['city'] ?? '',
                    'district' => $pin['district'] ?? '',
                    'state_code' => $pin['state_code'] ?? '',
                    'is_oda' => ($pin['is_oda'] ?? 'N') === 'Y',
                ];
            } catch (\Exception $e) {
                Log::error('Delhivery pincode check failed: ' . $e->getMessage());
                return ['serviceable' => false, 'message' => 'Service error'];
            }
        });
    }

    // ─── Shipping Cost Calculator ─────────────────────────────

    public function calculateCost(string $destPin, float $weightGrams = 500, string $paymentType = 'Pre-paid', float $codAmount = 0): array
    {
        try {
            $response = $this->api('GET', '/api/kinko/v1/invoice/charges/.json', [
                'md' => 'S',
                'ss' => 'Delivered',
                'd_pin' => $destPin,
                'o_pin' => $this->originPin,
                'cgm' => (int) $weightGrams,
                'pt' => $paymentType,
                'cod' => $codAmount,
            ]);

            if (!$response->successful()) {
                return ['success' => false, 'message' => 'Cost calculation failed'];
            }

            $data = $response->json();
            if (empty($data) || !is_array($data)) {
                return ['success' => false, 'message' => 'No data returned'];
            }

            $charge = $data[0] ?? [];

            return [
                'success' => true,
                'zone' => $charge['zone'] ?? '',
                'gross_amount' => $charge['gross_amount'] ?? 0,
                'total_amount' => $charge['total_amount'] ?? 0,
                'charged_weight' => $charge['charged_weight'] ?? $weightGrams,
                'tax' => $charge['tax_data'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Delhivery cost calc failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ─── Fetch Waybill ────────────────────────────────────────

    public function fetchWaybill(int $count = 1): array
    {
        try {
            $response = $this->api('GET', '/waybill/api/bulk/json/', ['count' => $count]);

            if ($response->successful()) {
                $waybills = $response->json();
                return is_array($waybills) ? $waybills : [$waybills];
            }
        } catch (\Exception $e) {
            Log::error('Delhivery waybill fetch failed: ' . $e->getMessage());
        }

        return [];
    }

    // ─── Fetch Registered Warehouses ─────────────────────────

    public function fetchWarehouses(): array
    {
        try {
            $response = $this->api('GET', '/api/backend/clientwarehouse/');
            if ($response->successful()) {
                $data = $response->json();
                $warehouses = $data['warehouses'] ?? $data['results'] ?? (is_array($data) ? $data : []);
                return array_map(fn($w) => [
                    'name'    => $w['registered_name'] ?? $w['name'] ?? '',
                    'city'    => $w['city'] ?? '',
                    'pincode' => $w['pin'] ?? '',
                ], array_filter($warehouses, fn($w) => !empty($w['registered_name'] ?? $w['name'] ?? '')));
            }
        } catch (\Exception $e) {
            Log::error('Delhivery fetch warehouses failed: ' . $e->getMessage());
        }
        return [];
    }

    // ─── Create Shipment ──────────────────────────────────────

    public function createShipment(array $shipmentData): array
    {
        try {
            $payload = [
                'shipments' => [$shipmentData],
                'pickup_location' => ['name' => $this->pickupLocation],
            ];

            $response = Http::withHeaders([
                'Authorization' => "Token {$this->token}",
                'Content-Type' => 'application/json',
            ])->timeout(30)->asForm()->post("{$this->baseUrl}/api/cmu/create.json", [
                'format' => 'json',
                'data' => json_encode($payload),
            ]);

            $body = $response->body();
            $data = $response->json() ?? [];

            // Detect warehouse name mismatch specifically
            if (str_contains($body, 'ClientWarehouse matching query does not exist')) {
                $warehouses = $this->fetchWarehouses();
                $names = array_column($warehouses, 'name');
                Log::warning('Delhivery warehouse mismatch', [
                    'configured_name' => $this->pickupLocation,
                    'available'       => $names,
                ]);

                $suggestion = count($names) ? ' Available: ' . implode(', ', $names) . '.' : '';
                return [
                    'success' => false,
                    'message' => "Pickup location \"{$this->pickupLocation}\" not found in your Delivery account.{$suggestion}",
                ];
            }

            if (!empty($data['packages'])) {
                $pkg = $data['packages'][0];
                $remarks = $pkg['remarks'] ?? [];
                $remarkStr = is_array($remarks) ? implode('; ', $remarks) : (string) $remarks;

                if (($pkg['status'] ?? '') === 'Fail') {
                    return [
                        'success' => false,
                        'message' => $remarkStr ?: 'Shipment creation failed.',
                        'remarks' => $remarks,
                    ];
                }

                return [
                    'success' => true,
                    'waybill' => $pkg['waybill'] ?? '',
                    'order_ref' => $pkg['refnum'] ?? '',
                    'status' => $pkg['status'] ?? 'Unknown',
                    'remarks' => $remarks,
                    'upload_wbn' => $data['upload_wbn'] ?? '',
                ];
            }

            $rmk = $data['rmk'] ?? $body ?? 'Unknown error from Delhivery';
            return ['success' => false, 'message' => $rmk];

        } catch (\Exception $e) {
            Log::error('Delhivery create shipment failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Book delivery for an order (one-click from admin).
     */
    public function bookDelivery(\App\Models\Order $order): array
    {
        $address = $order->shippingAddress ?? $order->billingAddress;

        if (!$address) {
            return ['success' => false, 'message' => 'No shipping address on order'];
        }

        $isCod = $order->payment_method === 'cod';
        $totalWeight = $order->items->sum(fn ($item) => ($item->product->weight ?? 500) * $item->quantity);

        $shipmentData = [
            'name' => $address->name ?? $order->customer_name ?? 'Customer',
            'add' => trim(($address->address_line_1 ?? '') . ' ' . ($address->address_line_2 ?? '')),
            'pin' => $address->postal_code ?? '',
            'city' => $address->city ?? '',
            'state' => $address->state ?? '',
            'country' => 'India',
            'phone' => $order->phone ?? $address->phone ?? '',
            'order' => $order->order_number,
            'payment_mode' => $isCod ? 'COD' : 'Prepaid',
            'return_pin' => $this->originPin,
            'return_city' => Setting::get('delhivery_return_city', 'Delhi'),
            'return_phone' => Setting::get('delhivery_return_phone', '9354567705'),
            'return_add' => Setting::get('delhivery_return_address', 'G-118, Deep Vihar, Rohini Sector 24'),
            'return_state' => Setting::get('delhivery_return_state', 'Delhi'),
            'return_country' => 'India',
            'return_name' => Setting::get('delhivery_return_name', 'Trendymus Returns'),
            'products_desc' => $order->items->pluck('product_name')->implode(', '),
            'hsn_code' => '',
            'cod_amount' => $isCod ? (string) $order->total : '0',
            'order_date' => $order->created_at->format('Y-m-d'),
            'total_amount' => (string) $order->total,
            'seller_add' => Setting::get('delhivery_return_address', 'G-118, Deep Vihar, Rohini Sector 24, Delhi'),
            'seller_name' => config('app.name', 'Trendymus'),
            'seller_inv' => $order->invoice_number ?? $order->order_number,
            'quantity' => (string) $order->items->sum('quantity'),
            'waybill' => '',
            'shipment_width' => '15',
            'shipment_height' => '10',
            'weight' => (string) max(500, $totalWeight),
            'shipment_length' => '20',
        ];

        return $this->createShipment($shipmentData);
    }

    // ─── Track Shipment ───────────────────────────────────────

    public function track(string $waybill): array
    {
        try {
            $response = $this->api('GET', '/api/v1/packages/json/', ['waybill' => $waybill]);

            if ($response->successful()) {
                $data = $response->json();
                $shipment = $data['ShipmentData'][0]['Shipment'] ?? null;

                if ($shipment) {
                    return [
                        'success' => true,
                        'status' => $shipment['Status']['Status'] ?? 'Unknown',
                        'status_location' => $shipment['Status']['StatusLocation'] ?? '',
                        'status_datetime' => $shipment['Status']['StatusDateTime'] ?? '',
                        'expected_date' => $shipment['ExpectedDeliveryDate'] ?? '',
                        'origin' => $shipment['Origin'] ?? '',
                        'destination' => $shipment['Destination'] ?? '',
                        'scans' => collect($shipment['Scans'] ?? [])->map(fn ($s) => [
                            'status' => $s['ScanDetail']['Scan'] ?? '',
                            'location' => $s['ScanDetail']['ScannedLocation'] ?? '',
                            'datetime' => $s['ScanDetail']['ScanDateTime'] ?? '',
                            'instructions' => $s['ScanDetail']['Instructions'] ?? '',
                        ])->toArray(),
                        'rto' => [
                            'is_rto' => ($shipment['ReverseInTransit'] ?? false),
                        ],
                    ];
                }
            }

            return ['success' => false, 'message' => $response->json('Error', 'Tracking failed')];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ─── Cancel Shipment ──────────────────────────────────────

    public function cancel(string $waybill): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Token {$this->token}",
            ])->timeout(15)->asForm()->post("{$this->baseUrl}/api/p/edit", [
                'waybill' => $waybill,
                'cancellation' => 'true',
            ]);

            return [
                'success' => $response->successful(),
                'message' => $response->json('status', $response->body()),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ─── Generate Label ───────────────────────────────────────

    public function generateLabel(string $waybill): ?string
    {
        try {
            $response = $this->api('GET', '/api/p/packing_slip', [
                'wbns' => $waybill,
                'pdf' => 'true',
            ]);

            if ($response->successful() && $response->header('content-type') === 'application/pdf') {
                $path = "labels/delhivery-{$waybill}.pdf";
                \Illuminate\Support\Facades\Storage::put($path, $response->body());
                return $path;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Delhivery label generation failed: ' . $e->getMessage());
            return null;
        }
    }

    // ─── Pickup Request ───────────────────────────────────────

    public function requestPickup(int $packageCount = 1, ?string $pickupDate = null, ?string $pickupTime = null): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Token {$this->token}",
            ])->timeout(15)->asForm()->post("{$this->baseUrl}/fm/request/new/", [
                'pickup_location' => $this->pickupLocation,
                'expected_package_count' => $packageCount,
                'pickup_date' => $pickupDate ?? now()->addDay()->format('Y-m-d'),
                'pickup_time' => $pickupTime ?? '12:00:00',
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ─── NDR Actions ──────────────────────────────────────────

    public function ndrAction(string $waybill, string $action, string $comment = ''): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Token {$this->token}",
            ])->timeout(15)->asForm()->post("{$this->baseUrl}/api/p/update", [
                'waybill' => $waybill,
                'act' => $action, // re-attempt, return, hold
                'ndr_comment' => $comment,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ─── Warehouse Management ─────────────────────────────────

    public function updateWarehouse(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Token {$this->token}",
                'Content-Type' => 'application/json',
            ])->timeout(15)->post("{$this->baseUrl}/api/backend/clientwarehouse/edit/", $data);

            return [
                'success' => str_contains($response->body(), 'True'),
                'data' => $response->body(),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ─── API Helper ───────────────────────────────────────────

    private function api(string $method, string $endpoint, array $params = [])
    {
        $request = Http::withHeaders([
            'Authorization' => "Token {$this->token}",
        ])->timeout(15);

        return match (strtoupper($method)) {
            'POST' => $request->post("{$this->baseUrl}{$endpoint}", $params),
            default => $request->get("{$this->baseUrl}{$endpoint}", $params),
        };
    }
}
