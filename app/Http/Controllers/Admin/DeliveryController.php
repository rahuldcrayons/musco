<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
// use App\Services\DelhiveryService; // Disabled for UK
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    private $delhivery = null; // DelhiveryService removed for UK

    public function __construct() {}

    /**
     * Delivery dashboard — stats, pending shipments, NDR, RTO.
     */
    public function index(): View
    {
        $stats = [
            'pending' => Order::whereIn('status', ['confirmed', 'packed'])->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'out_for_delivery' => Order::where('status', 'out_for_delivery')->count(),
            'delivered' => Order::where('status', 'delivered')->whereDate('delivered_at', '>=', now()->subDays(7))->count(),
            'rto' => Order::where('status', 'returned')->whereDate('updated_at', '>=', now()->subDays(30))->count(),
        ];

        // Analytics (30-day)
        $totalShipped30d = Order::where('carrier', 'Delhivery')
            ->whereDate('shipped_at', '>=', now()->subDays(30))->count();
        $delivered30d = Order::where('carrier', 'Delhivery')->where('status', 'delivered')
            ->whereDate('delivered_at', '>=', now()->subDays(30))->count();
        $rto30d = Order::where('carrier', 'Delhivery')->where('status', 'returned')
            ->whereDate('updated_at', '>=', now()->subDays(30))->count();

        $analytics = [
            'total_shipped' => $totalShipped30d,
            'delivery_rate' => $totalShipped30d > 0 ? round(($delivered30d / $totalShipped30d) * 100, 1) : 0,
            'rto_rate' => $totalShipped30d > 0 ? round(($rto30d / $totalShipped30d) * 100, 1) : 0,
            'avg_delivery_days' => Order::where('carrier', 'Delhivery')
                ->where('status', 'delivered')
                ->whereNotNull('shipped_at')
                ->whereNotNull('delivered_at')
                ->whereDate('delivered_at', '>=', now()->subDays(30))
                ->selectRaw('AVG(DATEDIFF(delivered_at, shipped_at)) as avg_days')
                ->value('avg_days') ?? 0,
        ];

        $pendingOrders = Order::whereIn('status', ['confirmed', 'packed'])
            ->with(['items.product', 'user'])
            ->latest()
            ->take(50)
            ->get();

        $shippedOrders = Order::whereIn('status', ['shipped', 'out_for_delivery'])
            ->whereNotNull('tracking_number')
            ->with(['items.product', 'user'])
            ->latest()
            ->take(30)
            ->get();

        return view('admin.delivery.index', compact('stats', 'pendingOrders', 'shippedOrders', 'analytics'));
    }

    /**
     * Book delivery via Delhivery for an order.
     */
    public function book(Order $order): RedirectResponse
    {
        return back()->with('error', 'Automatic delivery booking is not available. Please arrange shipping manually.');
    }

    /**
     * Track a shipment via Delhivery.
     */
    public function track(Order $order): JsonResponse
    {
        if (!$this->delhivery) return response()->json(['success' => false, 'message' => 'Tracking not available']);
        if (empty($order->tracking_number)) {
            return response()->json(['success' => false, 'message' => 'No tracking number']);
        }

        $result = $this->delhivery->track($order->tracking_number);

        return response()->json($result);
    }

    /**
     * Cancel a Delhivery shipment.
     */
    public function cancel(Order $order): RedirectResponse
    {
        if (!$this->delhivery) return back()->with('error', 'Cancellation not available');
        if (empty($order->tracking_number)) {
            return back()->with('error', 'No tracking number to cancel.');
        }

        $result = $this->delhivery->cancel($order->tracking_number);

        if ($result['success']) {
            $order->update([
                'status' => 'confirmed',
                'tracking_number' => null,
                'carrier' => null,
                'shipped_at' => null,
            ]);
            return back()->with('success', 'Shipment cancelled.');
        }

        return back()->with('error', 'Cancel failed: ' . ($result['message'] ?? 'Unknown'));
    }

    /**
     * Download shipping label.
     */
    public function label(Order $order)
    {
        if (!$this->delhivery) return back()->with('error', 'Labels not available');
        if (empty($order->tracking_number)) {
            return back()->with('error', 'No tracking number.');
        }

        $path = $this->delhivery->generateLabel($order->tracking_number);

        if ($path) {
            return response()->download(storage_path("app/{$path}"));
        }

        return back()->with('error', 'Label generation failed.');
    }

    /**
     * Request pickup from Delhivery.
     */
    public function requestPickup(Request $request): RedirectResponse
    {
        if (!$this->delhivery) return back()->with('error', 'Pickup not available');
        $count = (int) $request->input('package_count', 1);
        $date = $request->input('pickup_date', now()->addDay()->format('Y-m-d'));

        $result = $this->delhivery->requestPickup($count, $date);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? 'Pickup requested!' : ('Pickup request failed: ' . ($result['message'] ?? ''))
        );
    }

    /**
     * NDR action — re-attempt, return, hold.
     */
    public function ndrAction(Request $request, Order $order): RedirectResponse
    {
        if (!$this->delhivery) return back()->with('error', 'NDR not available');
        $action = $request->input('action'); // re-attempt, return, hold
        $comment = $request->input('comment', '');

        if (empty($order->tracking_number)) {
            return back()->with('error', 'No tracking number.');
        }

        $result = $this->delhivery->ndrAction($order->tracking_number, $action, $comment);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? "NDR action '{$action}' applied." : 'NDR action failed.'
        );
    }

    /**
     * Return registered Delhivery warehouses as JSON (AJAX).
     */
    public function warehouses(): JsonResponse
    {
        if (!$this->delhivery) return response()->json(['warehouses' => []]);
        $warehouses = $this->delhivery->fetchWarehouses();
        $configured = Setting::get('delhivery_pickup_location', 'Trendymus Warehouse');

        return response()->json([
            'warehouses' => $warehouses,
            'configured' => $configured,
            'match' => collect($warehouses)->pluck('name')->contains($configured),
        ]);
    }

    /**
     * Check pincode serviceability (AJAX).
     */
    public function checkPincode(Request $request): JsonResponse
    {
        return response()->json(['serviceable' => false, 'message' => 'Postcode check not available']);
    }

    /**
     * Calculate shipping cost (AJAX).
     */
    public function calculateCost(Request $request): JsonResponse
    {
        if (!$this->delhivery) return response()->json(['success' => false]);
        $pin = $request->input('pincode');
        $weight = (float) $request->input('weight', 500);
        $paymentType = $request->input('payment_type', 'Pre-paid');
        $codAmount = (float) $request->input('cod_amount', 0);

        $result = $this->delhivery->calculateCost($pin, $weight, $paymentType, $codAmount);

        return response()->json($result);
    }
}
