<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderDelivered;
use App\Events\OrderShipped;
use App\Events\OrderStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View|StreamedResponse
    {
        $query = Order::with(['user', 'items']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhere('guest_phone', 'like', "%{$s}%")
                  ->orWhere('guest_email', 'like', "%{$s}%")
                  ->orWhere('guest_name', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('email', 'like', "%{$s}%")
                      ->orWhere('first_name', 'like', "%{$s}%")
                      ->orWhere('last_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = min((int) $request->input('per_page', 10), 100);
        $orders = $query->latest()->paginate($perPage)->withQueryString();

        $stats = [
            'total'         => Order::count(),
            'confirmed'     => Order::where('status', 'confirmed')->count(),
            'processing'    => Order::whereIn('status', ['processing', 'packed'])->count(),
            'shipped'       => Order::whereIn('status', ['shipped', 'out_for_delivery'])->count(),
            'completed'     => Order::where('status', 'delivered')->count(),
            'cancelled'     => Order::where('status', 'cancelled')->count(),
            'items_ordered' => OrderItem::sum('quantity'),
            'returns'       => Order::where('status', 'returned')->count(),
        ];

        // Handle CSV export
        if ($request->input('export') === 'csv') {
            return $this->exportCsv($query->get());
        }

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'user',
            'items.product',
            'items.variant',
            'statusHistory',
            'shipments',
            'coupon',
            'deliveryPartner.user',
        ]);

        $trackingSteps = $order->getTrackingSteps();
        $latestShipment = $order->shipments->first();
        $activePartners = DeliveryPartner::with('user')->where('is_active', true)->get();

        return view('admin.orders.show', compact('order', 'trackingSteps', 'latestShipment', 'activePartners'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,packed,shipped,out_for_delivery,delivered,cancelled,returned,on_hold'],
            'comment' => ['nullable', 'string', 'max:500'],
            'carrier' => ['nullable', 'required_if:status,shipped', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'required_if:status,shipped', 'string', 'max:100'],
        ]);

        $oldStatus = $order->status;

        // Validate state transitions
        $allowedTransitions = [
            'pending'   => ['confirmed', 'cancelled', 'on_hold'],
            'confirmed' => ['processing', 'cancelled', 'on_hold'],
            'processing' => ['packed', 'cancelled', 'on_hold'],
            'packed' => ['shipped', 'cancelled', 'on_hold'],
            'shipped' => ['out_for_delivery', 'returned'],
            'out_for_delivery' => ['delivered', 'returned'],
            'delivered' => ['returned'],
            'cancelled' => [],
            'returned' => [],
            'on_hold' => ['confirmed', 'cancelled'],
        ];

        $allowed = $allowedTransitions[$oldStatus] ?? [];
        if (!in_array($validated['status'], $allowed)) {
            return back()->with('error', "Cannot change status from \"{$oldStatus}\" to \"{$validated['status']}\".");
        }

        // If shipping, create shipment record
        if ($validated['status'] === 'shipped' && !empty($validated['tracking_number'])) {
            $order->shipments()->create([
                'carrier' => $validated['carrier'],
                'tracking_number' => $validated['tracking_number'],
                'status' => 'in_transit',
                'shipped_at' => now(),
            ]);
        }

        // Update shipment status for out_for_delivery and delivered
        if (in_array($validated['status'], ['out_for_delivery', 'delivered'])) {
            $shipment = $order->shipments()->latest()->first();
            if ($shipment) {
                $shipmentStatus = $validated['status'] === 'out_for_delivery' ? 'out_for_delivery' : 'delivered';
                $shipment->update(['status' => $shipmentStatus]);
                if ($validated['status'] === 'delivered') {
                    $shipment->update(['delivered_at' => now()]);
                }
            }
        }

        $order->updateStatus($validated['status'], auth('admin')->id(), $validated['comment'] ?? null);

        OrderStatusChanged::dispatch($order, $oldStatus, $validated['status']);

        if ($validated['status'] === 'shipped') {
            OrderShipped::dispatch($order, $validated['tracking_number'] ?? null);
        } elseif ($validated['status'] === 'delivered') {
            OrderDelivered::dispatch($order);
        }

        return back()->with('success', "Order status updated from {$oldStatus} to {$validated['status']}");
    }

    public function ship(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'carrier' => ['required', 'string', 'max:100'],
            'tracking_number' => ['required', 'string', 'max:100'],
        ]);

        $order->shipments()->create([
            'carrier' => $validated['carrier'],
            'tracking_number' => $validated['tracking_number'],
            'status' => 'in_transit',
            'shipped_at' => now(),
        ]);

        $order->updateStatus('shipped', auth('admin')->id(), "Shipped via {$validated['carrier']} - Tracking: {$validated['tracking_number']}");

        OrderShipped::dispatch($order, $validated['tracking_number']);

        return back()->with('success', 'Order marked as shipped');
    }

    public function invoice(Order $order): View
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.invoice', compact('order'));
    }

    public function assignPartner(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_partner_id' => 'nullable|exists:delivery_partners,id',
        ]);

        $order->update(['delivery_partner_id' => $validated['delivery_partner_id']]);

        // Also update latest shipment
        $shipment = $order->shipments()->latest()->first();
        if ($shipment) {
            $shipment->update(['delivery_partner_id' => $validated['delivery_partner_id']]);
        }

        if ($validated['delivery_partner_id']) {
            $partner = DeliveryPartner::with('user')->find($validated['delivery_partner_id']);
            $order->statusHistory()->create([
                'status' => $order->status,
                'comment' => "Delivery partner assigned: {$partner->user->full_name} ({$partner->partner_id})",
                'created_by' => auth('admin')->id(),
            ]);
        }

        return back()->with('success', 'Delivery partner assigned successfully.');
    }

    public function setExpectedDelivery(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'expected_delivery_date' => 'nullable|date|after_or_equal:today',
        ]);

        $order->update(['expected_delivery_date' => $request->expected_delivery_date ?: null]);

        return back()->with('success', $request->expected_delivery_date
            ? 'Expected delivery date set to ' . \Carbon\Carbon::parse($request->expected_delivery_date)->format('M d, Y') . '.'
            : 'Expected delivery date cleared.');
    }

    private function exportCsv($orders): StreamedResponse
    {
        $filename = 'orders-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order #', 'Date', 'Customer', 'Email', 'Phone', 'Items', 'Total', 'Payment', 'Status', 'Shipping Address']);

            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->user?->full_name ?? 'Guest',
                    $order->user?->email ?? $order->guest_email ?? '',
                    $order->guest_phone ?? ($order->user?->phone ?? ''),
                    $order->items->sum('quantity'),
                    $order->total,
                    $order->payment_status,
                    $order->status,
                    trim(implode(', ', array_filter([
                        $order->shipping_address_snapshot['address_line_1'] ?? '',
                        $order->shipping_address_snapshot['city'] ?? '',
                        $order->shipping_address_snapshot['state'] ?? '',
                        $order->shipping_address_snapshot['postal_code'] ?? '',
                    ]))),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function packingSlip(Order $order): View
    {
        $order->load(['items.product']);

        return view('admin.orders.packing-slip', compact('order'));
    }
}
