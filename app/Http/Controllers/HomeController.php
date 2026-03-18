<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\HomepageSection;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Homepage display settings (all from single cached query now)
        $featuredCount    = (int) Setting::get('homepage_featured_count', 10);
        $newArrivalsCount = (int) Setting::get('homepage_new_arrivals_count', 10);
        $bestsellersCount = (int) Setting::get('homepage_bestsellers_count', 10);
        $dealsCount       = (int) Setting::get('homepage_deals_count', 10);
        $testimonialsCount = (int) Setting::get('homepage_testimonials_count', 6);
        $newArrivalsDays  = (int) Setting::get('homepage_new_arrivals_days', 30);

        // Shared scope for active products with eager loads
        $productEager = ['category:id,name,slug', 'brand:id,name,slug', 'primaryImage'];

        // Featured products
        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with($productEager)
            ->orderBy('created_at', 'desc')
            ->take($featuredCount)
            ->get();

        // New arrivals
        $newArrivals = Product::query()
            ->where('is_active', true)
            ->where('created_at', '>=', now()->subDays($newArrivalsDays))
            ->with($productEager)
            ->orderBy('created_at', 'desc')
            ->take($newArrivalsCount)
            ->get();

        // Bestsellers
        $bestsellers = Product::query()
            ->where('is_active', true)
            ->with($productEager)
            ->orderBy('sales_count', 'desc')
            ->take($bestsellersCount)
            ->get();

        // Deal products (where price < mrp)
        $deals = Product::query()
            ->where('is_active', true)
            ->whereColumn('price', '<', 'mrp')
            ->with($productEager)
            ->orderByRaw('(mrp - price) / mrp DESC')
            ->take($dealsCount)
            ->get();

        // Category carousel — select only needed columns, single product image per category
        $carouselCategories = Category::query()
            ->select('id', 'name', 'slug', 'image_url', 'icon')
            ->where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->with(['products' => fn($q) => $q->where('is_active', true)
                ->select('id', 'category_id')
                ->with('primaryImage')
                ->limit(1)])
            ->having('products_count', '>', 0)
            ->orderBy('position')
            ->take(15)
            ->get();

        // Categories — empty slug list means no results, skip query entirely
        $categories = collect();

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

        // Site settings (all from batch cache — no extra queries)
        $siteSettings = [
            'site_name' => Setting::get('site_name', 'Jikra'),
            'site_tagline' => Setting::get('site_tagline', 'Adorable Clothing for Little Ones'),
            'site_logo' => Setting::get('site_logo', ''),
            'footer_about' => Setting::get('footer_about', 'Your one-stop shop for mobile accessories, Bluetooth speakers, earphones, chargers, and more. Quality tech accessories at great prices.'),
        ];

        return view('home', compact(
            'featuredProducts',
            'newArrivals',
            'bestsellers',
            'deals',
            'categories',
            'carouselCategories',
            'banners',
            'sections',
            'testimonials',
            'siteSettings',
            'flashSale'
        ));
    }
}
