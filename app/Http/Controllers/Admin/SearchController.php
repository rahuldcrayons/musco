<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function products(Request $request): JsonResponse
    {
        $q = $request->input('search', $request->input('q', ''));

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $products = Product::with('images')
            ->select('id', 'name', 'price', 'slug')
            ->where('name', 'like', "%{$q}%")
            ->orWhere('sku', 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'                => $p->id,
                'name'              => $p->name,
                'price'             => $p->price,
                'primary_image_url' => $p->primary_image_url,
            ]);

        return response()->json($products);
    }

    public function orders(Request $request): JsonResponse
    {
        $q = $request->input('search', '');

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $orders = Order::with('user')
            ->select('id', 'order_number', 'total', 'status', 'payment_status', 'user_id', 'created_at')
            ->where(function ($query) use ($q) {
                $query->where('order_number', 'like', "%{$q}%")
                    ->orWhere('guest_phone', 'like', "%{$q}%")
                    ->orWhere('guest_name', 'like', "%{$q}%")
                    ->orWhereHas('user', fn($uq) => $uq->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%"));
            })
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'total'        => $o->total,
                'status'       => $o->status,
                'customer'     => $o->user?->full_name ?? 'Guest',
            ]);

        return response()->json($orders);
    }

    public function customers(Request $request): JsonResponse
    {
        $q = $request->input('search', '');

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $customers = User::select('id', 'first_name', 'last_name', 'email', 'phone')
            ->where(function ($query) use ($q) {
                $query->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->orderBy('first_name')
            ->limit(10)
            ->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'first_name' => $u->first_name,
                'last_name'  => $u->last_name ?? '',
                'email'      => $u->email,
            ]);

        return response()->json($customers);
    }
}
