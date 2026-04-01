<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = Seller::all();
        $categories = Category::whereNotNull('parent_id')->get()->keyBy('slug');
        $brands = Brand::all();

        if ($sellers->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Please run SellerSeeder and JewelleryCategorySeeder first.');
            return;
        }

        $products = [
            // Rings
            ['name' => 'Solitaire Diamond Ring', 'mrp' => 12999, 'price' => 8999, 'stock' => 25, 'cat' => 'engagement-rings'],
            ['name' => 'Gold Band Ring 22K', 'mrp' => 8499, 'price' => 6999, 'stock' => 40, 'cat' => 'wedding-bands'],
            ['name' => 'Emerald Statement Ring', 'mrp' => 15999, 'price' => 11999, 'stock' => 15, 'cat' => 'cocktail-rings'],
            ['name' => 'Silver Oxidised Ring', 'mrp' => 1299, 'price' => 799, 'stock' => 100, 'cat' => 'silver-rings'],
            ['name' => 'Rose Gold Infinity Ring', 'mrp' => 5999, 'price' => 3999, 'stock' => 35, 'cat' => 'fashion-rings'],

            // Necklaces & Pendants
            ['name' => 'Pearl Drop Necklace', 'mrp' => 6999, 'price' => 4499, 'stock' => 30, 'cat' => 'pendants'],
            ['name' => 'Gold Chain 22K - 18 inch', 'mrp' => 24999, 'price' => 21999, 'stock' => 20, 'cat' => 'chains'],
            ['name' => 'Diamond Pendant with Chain', 'mrp' => 18999, 'price' => 14999, 'stock' => 18, 'cat' => 'pendant-sets'],
            ['name' => 'Ruby Pendant Necklace', 'mrp' => 9999, 'price' => 7499, 'stock' => 22, 'cat' => 'pendants'],
            ['name' => 'Layered Chain Necklace', 'mrp' => 3499, 'price' => 2299, 'stock' => 50, 'cat' => 'layered-necklaces'],

            // Earrings
            ['name' => 'Gold Hoop Earrings', 'mrp' => 4999, 'price' => 3499, 'stock' => 45, 'cat' => 'hoops'],
            ['name' => 'Diamond Stud Earrings', 'mrp' => 11999, 'price' => 8999, 'stock' => 20, 'cat' => 'studs'],
            ['name' => 'Pearl Stud Earrings', 'mrp' => 2999, 'price' => 1999, 'stock' => 60, 'cat' => 'studs'],
            ['name' => 'Jhumka Earrings - Gold Plated', 'mrp' => 2499, 'price' => 1599, 'stock' => 55, 'cat' => 'jhumkas'],

            // Bangles & Bracelets
            ['name' => 'Gold Bangle Set - 22K', 'mrp' => 32999, 'price' => 28999, 'stock' => 12, 'cat' => 'gold-bangles'],
            ['name' => 'Diamond Tennis Bracelet', 'mrp' => 19999, 'price' => 15999, 'stock' => 10, 'cat' => 'tennis-bracelets'],
            ['name' => 'Silver Charm Bracelet', 'mrp' => 2999, 'price' => 1999, 'stock' => 70, 'cat' => 'charm-bracelets'],
            ['name' => 'Cubic Zirconia Bracelet', 'mrp' => 3499, 'price' => 2499, 'stock' => 40, 'cat' => 'bracelets'],

            // Wedding & Traditional
            ['name' => 'Bridal Gold Necklace Set', 'mrp' => 49999, 'price' => 44999, 'stock' => 8, 'cat' => 'bridal-sets'],
            ['name' => 'Gold Mangalsutra with Diamond', 'mrp' => 14999, 'price' => 11999, 'stock' => 25, 'cat' => 'mangalsutra'],
            ['name' => 'Temple Jewellery Set', 'mrp' => 8999, 'price' => 6499, 'stock' => 15, 'cat' => 'bridal-sets'],

            // Nose Pins & Others
            ['name' => 'Diamond Nose Pin', 'mrp' => 4999, 'price' => 3499, 'stock' => 50, 'cat' => 'nose-pins'],
            ['name' => 'Gold Nose Ring - Traditional', 'mrp' => 2999, 'price' => 1999, 'stock' => 35, 'cat' => 'nose-rings'],
            ['name' => 'Silver Anklet Pair', 'mrp' => 1999, 'price' => 1299, 'stock' => 45, 'cat' => 'anklets'],

            // Men's Jewellery
            ['name' => 'Men\'s Gold Bracelet - 22K', 'mrp' => 18999, 'price' => 15999, 'stock' => 20, 'cat' => 'mens-bracelets'],
            ['name' => 'Silver Men\'s Ring - Oxidised', 'mrp' => 1499, 'price' => 999, 'stock' => 80, 'cat' => 'mens-rings'],

            // Gold & Silver Coins
            ['name' => 'Gold Coin 10g - 24K', 'mrp' => 62999, 'price' => 61499, 'stock' => 30, 'cat' => 'gold-coins'],
            ['name' => 'Silver Coin 50g - 999 Purity', 'mrp' => 4999, 'price' => 4499, 'stock' => 50, 'cat' => 'silver-coins'],
        ];

        $defaultSeller = $sellers->first();

        foreach ($products as $index => $productData) {
            $category = $categories->get($productData['cat']) ?? $categories->first();

            Product::updateOrCreate(
                ['slug' => Str::slug($productData['name'])],
                [
                    'uuid' => Str::uuid(),
                    'seller_id' => $defaultSeller->id,
                    'category_id' => $category->id,
                    'brand_id' => $brands->isNotEmpty() ? $brands->random()->id : null,
                    'name' => $productData['name'],
                    'short_description' => 'Exquisite ' . $productData['name'] . ' crafted with premium quality materials.',
                    'description' => '<p>Discover the elegance of our ' . $productData['name'] . '. Meticulously crafted with the finest materials, this piece combines timeless design with exceptional craftsmanship.</p><p>Each piece is BIS hallmarked and comes with a certificate of authenticity, ensuring you receive only the best quality jewellery.</p>',
                    'sku' => 'MUS-' . strtoupper(Str::random(8)),
                    'mrp' => $productData['mrp'],
                    'price' => $productData['price'],
                    'cost_price' => $productData['price'] * 0.6,
                    'stock_quantity' => $productData['stock'],
                    'low_stock_threshold' => 5,
                    'stock_status' => 'in_stock',
                    'is_active' => true,
                    'is_featured' => $index < 10,
                    'is_taxable' => true,
                    'tax_rate' => 3,
                    'rating' => rand(40, 50) / 10,
                    'review_count' => rand(20, 500),
                    'view_count' => rand(200, 8000),
                    'sales_count' => rand(30, 800),
                    'status' => 'approved',
                    'published_at' => now()->subDays(rand(1, 60)),
                ]
            );
        }
    }
}
