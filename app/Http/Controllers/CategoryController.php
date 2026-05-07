<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $with = [
            'children' => fn ($q) => $q->where('is_active', true)
                ->withCount(['products' => fn ($q2) => $q2->where('is_active', true)]),
            'products' => fn ($q) => $q->where('is_active', true)->with('primaryImage')->limit(1),
        ];
        $withCount = ['products' => fn ($q) => $q->where('is_active', true)];

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with($with)
            ->withCount($withCount)
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                $category->total_products_count = $category->products_count
                    + $category->children->sum('products_count');
                return $category;
            });

        return view('categories.index', compact('categories'));
    }

    public function show(Request $request, Category $category): View|JsonResponse|RedirectResponse
    {
        // Redirect legacy ID-based URLs to canonical slug URL
        $routeParam = $request->route()->parameter('category', '');
        if (is_object($routeParam)) {
            $routeParam = $request->segment(2);
        }
        if (is_numeric($routeParam) && $category->slug) {
            return redirect()->route('categories.show', $category, 301);
        }

        abort_unless($category->is_active, 404);

        // Get all descendant category IDs
        $categoryIds = collect([$category->id]);
        if ($category->children->count()) {
            $categoryIds = $categoryIds->merge(
                $category->children->pluck('id')
            );
        }

        $query = Product::query()
            ->where('is_active', true)
            ->whereIn('category_id', $categoryIds)
            ->with(['category', 'brand', 'primaryImage']);

        // Subcategory filter
        if ($request->filled('subcategory')) {
            $subSlugs = (array) $request->subcategory;
            $subIds = Category::whereIn('slug', $subSlugs)->pluck('id');
            if ($subIds->isNotEmpty()) {
                $query->whereIn('category_id', $subIds);
            }
        }

        // Price filter — convert display currency value to stored (GBP base) before comparing
        if ($request->filled('min_price')) {
            $query->where('price', '>=', display_price_to_stored($request->min_price));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', display_price_to_stored($request->max_price));
        }

        // Attributes filter (dynamic based on category)
        foreach ($request->except(['page', 'sort', 'brand', 'min_price', 'max_price', 'in_stock', 'on_sale']) as $key => $value) {
            if (str_starts_with($key, 'attr_')) {
                $attributeSlug = str_replace('attr_', '', $key);
                $values = is_array($value) ? $value : [$value];
                $query->whereHas('variants.attributeValues', function ($q) use ($attributeSlug, $values) {
                    $q->whereHas('attribute', function ($aq) use ($attributeSlug) {
                        $aq->where('slug', $attributeSlug);
                    })->whereIn('slug', $values);
                });
            }
        }

        // Keyword search within category
        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%")
                   ->orWhere('short_description', 'like', "%{$q}%");
            });
        }

        // In stock filter
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // On sale filter (price less than mrp)
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('mrp')->whereColumn('price', '<', 'mrp');
        }

        // Sorting
        $sortBy = $request->get('sort', 'newest');
        match ($sortBy) {
            'price_asc'   => $query->orderBy('price', 'asc')->orderBy('id', 'asc'),
            'price_desc'  => $query->orderBy('price', 'desc')->orderBy('id', 'desc'),
            'rating'      => $query->orderBy('rating', 'desc')->orderBy('id', 'desc'),
            'bestselling' => $query->orderBy('sales_count', 'desc')->orderBy('id', 'desc'),
            'name'        => $query->orderBy('name', 'asc')->orderBy('id', 'asc'),
            default       => $query->orderByRaw('EXISTS(SELECT 1 FROM product_images WHERE product_images.product_id = products.id) DESC')->orderByRaw('CASE WHEN stock_quantity > 0 THEN 0 ELSE 1 END ASC')->orderBy('created_at', 'desc')->orderBy('id', 'desc'),
        };

        $products = $query->paginate(12)->withQueryString();

        if ($request->ajax()) {
            $html = '';
            foreach ($products as $product) {
                $html .= view('components.product-card', ['product' => $product])->render();
            }
            return response()->json(['html' => $html, 'hasMore' => $products->hasMorePages()]);
        }

        // Subcategories for filter sidebar
        $filterSubcategories = $category->children()->where('is_active', true)->withCount('products')->get();

        // Subcategories for pill nav
        $subcategories = $filterSubcategories;

        // Breadcrumbs
        $breadcrumbs = [];
        if ($category->parent) {
            $breadcrumbs[] = ['label' => $category->parent->name, 'url' => route('categories.show', $category->parent)];
        }
        $breadcrumbs[] = ['label' => $category->name, 'url' => null];

        return view('categories.show', compact('category', 'products', 'filterSubcategories', 'subcategories', 'breadcrumbs'));
    }
}
