<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\HomepageSection;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $data = $this->loadHomeData();

        return view('home', $data);
    }

    protected function loadHomeData(): array
    {
        return Cache::remember('homepage_data', 600, function () {
            return $this->buildHomeData();
        });
    }

    protected function buildHomeData(): array
    {
        // Homepage display settings
        $featuredCount    = (int) Setting::get('homepage_featured_count', 8);
        $newArrivalsCount = (int) Setting::get('homepage_new_arrivals_count', 8);
        $bestsellersCount = (int) Setting::get('homepage_bestsellers_count', 8);
        $dealsCount       = (int) Setting::get('homepage_deals_count', 8);
        $testimonialsCount = (int) Setting::get('homepage_testimonials_count', 6);
        $newArrivalsDays  = (int) Setting::get('homepage_new_arrivals_days', 30);

        // Shared scope for active products with eager loads (only essential columns)
        $productEager = ['category:id,name,slug', 'brand:id,name,slug', 'primaryImage'];

        // Featured products
        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('is_featured', true)
            ->whereHas('primaryImage')
            ->with($productEager)
            ->inRandomOrder()
            ->take(8)
            ->get();

        // Tab categories for New Arrivals section (dynamic from DB)
        $tabCategories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)->where('is_featured', true)])
            ->having('products_count', '>', 0)
            ->orderBy('position')
            ->take(5)
            ->get(['id', 'name', 'slug']);

        // New arrivals
        $newArrivals = Product::query()
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('created_at', '>=', now()->subDays($newArrivalsDays))
            ->whereHas('primaryImage')
            ->with($productEager)
            ->inRandomOrder()
            ->take($newArrivalsCount)
            ->get();

        // Bestsellers
        $bestsellers = Product::query()
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->whereHas('primaryImage')
            ->with($productEager)
            ->inRandomOrder()
            ->take($bestsellersCount)
            ->get();

        // Deal products (where price < mrp)
        $deals = Product::query()
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->whereColumn('price', '<', 'mrp')
            ->whereHas('primaryImage')
            ->with($productEager)
            ->inRandomOrder()
            ->take($dealsCount)
            ->get();

        // Category carousel — root categories with product count (optimized single query)
        $carouselCategories = Category::query()
            ->select('id', 'name', 'slug', 'image_url', 'icon')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->orderBy('position')
            ->take(12)
            ->get();

        // Brands for homepage carousel
        $homeBrands = \App\Models\Brand::query()
            ->where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->having('products_count', '>', 0)
            ->orderByDesc('is_featured')
            ->orderBy('position')
            ->get();

        // Root categories with active children for homepage collections
        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->with(['children' => fn($q) => $q->where('is_active', true)
                ->withCount(['products' => fn($q2) => $q2->where('is_active', true)])
                ->with(['products' => fn($q2) => $q2->where('is_active', true)
                    ->select('id', 'category_id')
                    ->with('primaryImage')
                    ->limit(1)])
                ->orderBy('position')])
            ->with(['products' => fn($q) => $q->where('is_active', true)
                ->select('id', 'category_id')
                ->with('primaryImage')
                ->limit(1)])
            ->orderBy('position')
            ->take(6)
            ->get();

        // Pendant Sets spotlight — fixed to Pendant Sets category
        $spotlightCategory = Category::where('slug', 'jewellery-pendant-sets')
            ->where('is_active', true)
            ->first(['id', 'name', 'slug', 'description', 'is_featured']);

        $spotlightProducts = $spotlightCategory
            ? Product::query()
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->where('category_id', $spotlightCategory->id)
                ->whereHas('primaryImage')
                ->with($productEager)
                ->inRandomOrder()
                ->take(25)
                ->get()
            : collect();

        // 2×2 category banner cards — any 4 active categories with most products
        $bannerCategories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->having('products_count', '>', 0)
            ->orderByDesc('is_featured')
            ->orderByDesc('products_count')
            ->orderBy('position')
            ->take(4)
            ->with(['products' => fn($q) => $q
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->whereHas('primaryImage')
                ->with('primaryImage')
                ->limit(1)])
            ->get(['id', 'name', 'slug', 'image_url']);

        // Banners
        $banners = Banner::query()
            ->where('is_active', true)
            ->where('position', 'hero')
            ->orderBy('priority')
            ->get();

        // Homepage sections
        $sections = HomepageSection::active()->ordered()->get()->keyBy('key');

        // Testimonials
        $testimonials = Testimonial::active()->ordered()->take($testimonialsCount)->get();

        // Active flash sale for popup
        $flashSale = FlashSale::active()
            ->withCount('products')
            ->first();

        // Dynamic price ranges for "Shop by Range" section
        $sortedPrices = Product::query()
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->whereHas('primaryImage')
            ->orderBy('price')
            ->pluck('price');

        $priceRanges = collect();
        if ($sortedPrices->count() >= 4) {
            $c = $sortedPrices->count();
            $niceRound = function (float $v): int {
                if ($v <= 10)   return (int) ceil($v);
                if ($v <= 50)   return (int) ceil($v / 5) * 5;
                if ($v <= 100)  return (int) ceil($v / 10) * 10;
                if ($v <= 500)  return (int) ceil($v / 50) * 50;
                if ($v <= 2000) return (int) ceil($v / 100) * 100;
                return (int) ceil($v / 500) * 500;
            };
            $candidates = [];
            foreach ([0.15, 0.30, 0.50, 0.70, 0.85, 1.0] as $p) {
                $idx = $p >= 1.0 ? $c - 1 : (int) floor($c * $p);
                $candidates[] = $niceRound((float) $sortedPrices->get($idx));
            }
            $tierMaxes = array_values(array_unique($candidates));
            sort($tierMaxes);
            $tierMaxes = array_slice($tierMaxes, 0, 4);

            $prev = 0;
            foreach ($tierMaxes as $maxPrice) {
                if ($maxPrice <= $prev) continue;
                $product = Product::query()
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->where('price', '>', $prev)
                    ->where('price', '<=', $maxPrice)
                    ->whereHas('primaryImage')
                    ->with('primaryImage')
                    ->inRandomOrder()
                    ->first();
                $priceRanges->push([
                    'label' => 'Under ' . currency_symbol() . number_format($maxPrice),
                    'min'   => $prev,
                    'max'   => $maxPrice,
                    'img'   => $product?->primary_image_url,
                ]);
                $prev = $maxPrice;
            }
        }

        // Site settings (all from batch cache — no extra queries)
        $siteSettings = [
            'site_name' => Setting::get('site_name', 'Trendymus'),
            'site_tagline' => Setting::get('site_tagline', 'Exquisite Jewellery for Every Occasion'),
            'site_logo' => Setting::get('site_logo', ''),
            'footer_about' => Setting::get('footer_about', 'Your trusted destination for certified gold, diamond & silver jewellery. Hallmarked collections, exquisite craftsmanship, and timeless designs for every occasion.'),
        ];

        return compact(
            'featuredProducts',
            'tabCategories',
            'newArrivals',
            'bestsellers',
            'deals',
            'categories',
            'carouselCategories',
            'homeBrands',
            'banners',
            'bannerCategories',
            'spotlightCategory',
            'spotlightProducts',
            'sections',
            'testimonials',
            'siteSettings',
            'flashSale',
            'priceRanges'
        );
    }
}
