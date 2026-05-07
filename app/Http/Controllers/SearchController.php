<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\SearchLog;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return view('search.index', [
                'products' => new LengthAwarePaginator([], 0, 24),
                'query' => '',
                'categories' => collect(),
                'brands' => collect(),
            ]);
        }

        // Log search
        if ($query) {
            SearchLog::create([
                'user_id'       => auth()->id(),
                'session_id'    => $request->session()->getId(),
                'query'         => $query,
                'results_count' => 0, // Will be updated after search
            ]);
        }

        $productsQuery = Product::query()
            ->where('is_active', true)
            ->with(['category', 'brand', 'primaryImage']);

        // Full-text search using Scout if configured, otherwise basic search
        if (config('scout.driver')) {
            $productIds = Product::search($query)->keys();
            $productsQuery->whereIn('id', $productIds);
        } else {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$query}%"))
                  ->orWhereHas('brand', fn ($bq) => $bq->where('name', 'like', "%{$query}%"));
            });
        }

        // Apply filters
        if ($request->filled('category')) {
            $productsQuery->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('brand')) {
            $productsQuery->whereHas('brand', fn ($q) => $q->where('slug', $request->brand));
        }

        if ($request->filled('min_price')) {
            $productsQuery->where('price', '>=', display_price_to_stored($request->min_price));
        }

        if ($request->filled('max_price')) {
            $productsQuery->where('price', '<=', display_price_to_stored($request->max_price));
        }

        // Sorting
        $sortBy = $request->get('sort', 'relevance');
        match ($sortBy) {
            'price_asc' => $productsQuery->orderBy('price', 'asc'),
            'price_desc' => $productsQuery->orderBy('price', 'desc'),
            'rating' => $productsQuery->orderBy('rating', 'desc'),
            'newest' => $productsQuery->orderBy('created_at', 'desc'),
            default => $productsQuery->orderBy('sales_count', 'desc'),
        };

        $products = $productsQuery->paginate(24)->withQueryString();

        // Update search log with results count
        if ($query) {
            SearchLog::where('query', $query)
                ->where('created_at', '>=', now()->subMinute())
                ->latest()
                ->first()
                ?->update(['results_count' => $products->total()]);
        }

        // Get available filters
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->whereHas('products', fn ($q) => $q->where('is_active', true))
            ->get();

        $brands = Brand::where('is_active', true)
            ->whereHas('products', fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();

        // Facebook CAPI: Search
        $fbEventId = null;
        if (!empty($query)) {
            $fbEventId = AnalyticsService::generateEventId('srch');
            app(AnalyticsService::class)->trackSearch($query, $products->total(), $request, $fbEventId);
        }

        if ($request->ajax()) {
            $html = '';
            foreach ($products as $product) {
                $html .= view('components.product-card', ['product' => $product])->render();
            }
            return response()->json([
                'html'    => $html,
                'hasMore' => $products->hasMorePages(),
                'total'   => $products->total(),
                'query'   => $query,
            ]);
        }

        return view('search.index', compact('products', 'query', 'categories', 'brands', 'fbEventId'));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query        = trim($request->get('q', ''));
        $categorySlug = trim($request->get('category', ''));

        if (strlen($query) < 1) {
            return response()->json(['results' => [], 'products' => [], 'categories' => []]);
        }

        $cacheKey = 'search_suggest_' . md5(strtolower($query) . '|' . $categorySlug);

        $data = Cache::remember($cacheKey, 30, function () use ($query, $categorySlug) {
            $categoryId = null;
            if ($categorySlug) {
                $cat = \App\Models\Category::where('slug', $categorySlug)->first();
                if ($cat) {
                    // include the category itself + all its descendants
                    $categoryId = [$cat->id];
                    $children = \App\Models\Category::where('parent_id', $cat->id)->pluck('id')->toArray();
                    $categoryId = array_merge($categoryId, $children);
                }
            }

            $baseQuery = Product::query()
                ->where('is_active', true)
                ->with(['category:id,name,slug', 'primaryImage']);

            if ($categoryId) {
                $baseQuery->whereIn('category_id', $categoryId);
            }

            // Use Meilisearch/Scout if configured (fast index lookup)
            if (config('scout.driver') && !$categoryId) {
                $products = Product::search($query)
                    ->query(fn ($q) => $q->where('is_active', true)->with(['category:id,name,slug', 'primaryImage']))
                    ->take(8)
                    ->get()
                    ->map(fn ($p) => [
                        'id'       => $p->id,
                        'name'     => $p->name,
                        'url'      => route('product.show', $p),
                        'image'    => $p->primary_image_url,
                        'price'    => number_format((float) $p->price),
                        'mrp'      => number_format((float) $p->mrp),
                        'discount' => $p->mrp > $p->price ? round((($p->mrp - $p->price) / $p->mrp) * 100) : 0,
                        'category' => $p->category?->name,
                        'type'     => 'product',
                    ]);
            } else {
                // LIKE query, prefix match first
                $products = $baseQuery
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "{$query}%")
                          ->orWhere('name', 'like', "% {$query}%")
                          ->orWhere('name', 'like', "%{$query}%");
                    })
                    ->orderByRaw("CASE WHEN name LIKE ? THEN 0 WHEN name LIKE ? THEN 1 ELSE 2 END", ["{$query}%", "% {$query}%"])
                    ->orderBy('sales_count', 'desc')
                    ->take(8)
                    ->get()
                    ->map(fn ($p) => [
                        'id'       => $p->id,
                        'name'     => $p->name,
                        'url'      => route('product.show', $p),
                        'image'    => $p->primary_image_url,
                        'price'    => number_format((float) $p->price),
                        'mrp'      => number_format((float) $p->mrp),
                        'discount' => $p->mrp > $p->price ? round((($p->mrp - $p->price) / $p->mrp) * 100) : 0,
                        'category' => $p->category?->name,
                        'type'     => 'product',
                    ]);
            }

            // Categories — small table, LIKE is fine
            $categories = Category::query()
                ->where('is_active', true)
                ->where('name', 'like', "%{$query}%")
                ->select('id', 'name', 'slug', 'image_url')
                ->take(4)
                ->get()
                ->map(fn ($c) => [
                    'name'  => $c->name,
                    'url'   => route('categories.show', $c),
                    'image' => $c->image_url ? asset('storage/' . $c->image_url) : null,
                    'type'  => 'category',
                ]);

            // Brands — small table
            $brands = Brand::query()
                ->where('is_active', true)
                ->where('name', 'like', "%{$query}%")
                ->select('id', 'name', 'slug')
                ->take(2)
                ->get()
                ->map(fn ($b) => [
                    'name'  => $b->name,
                    'url'   => route('brands.show', $b),
                    'image' => null,
                    'type'  => 'brand',
                ]);

            return [
                'results'    => $products->merge($categories)->merge($brands)->values(),
                'products'   => $products->values(),
                'categories' => $categories->values(),
                'brands'     => $brands->values(),
                'query'      => $query,
            ];
        });

        return response()->json(array_merge($data, [
            'search_url' => route('search', ['q' => $query]),
        ]));
    }
}
