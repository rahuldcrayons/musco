<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBrands();
        $this->seedProducts();
        $this->seedReviews();
        $this->seedTestimonials();
        $this->seedBanners();

        $this->command->info('Demo data seeded successfully!');
    }

    private function seedBrands(): void
    {
        $brands = [
            ['name' => 'Trendimus Originals', 'description' => 'Our signature in-house collection of premium anti-tarnish jewelry.', 'is_featured' => true, 'position' => 1],
            ['name' => 'Tanishq', 'description' => 'India\'s most trusted jewelry brand by Titan Company.', 'is_featured' => true, 'position' => 2],
            ['name' => 'Malabar Gold', 'description' => 'Exquisite gold and diamond jewelry from Malabar Group.', 'is_featured' => true, 'position' => 3],
            ['name' => 'CaratLane', 'description' => 'Modern, everyday jewelry designed for the contemporary woman.', 'is_featured' => true, 'position' => 4],
            ['name' => 'Kalyan Jewellers', 'description' => 'Traditional and contemporary jewelry crafted with care.', 'is_featured' => false, 'position' => 5],
            ['name' => 'PC Jeweller', 'description' => 'Hallmarked gold and diamond jewelry at great value.', 'is_featured' => false, 'position' => 6],
            ['name' => 'Senco Gold', 'description' => 'Timeless designs from one of eastern India\'s most loved brands.', 'is_featured' => false, 'position' => 7],
            ['name' => 'Amrapali', 'description' => 'Heritage jewelry inspired by India\'s royal legacy.', 'is_featured' => false, 'position' => 8],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['name' => $brand['name']],
                array_merge($brand, [
                    'slug' => Str::slug($brand['name']),
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Brands seeded: ' . count($brands));
    }

    private function seedProducts(): void
    {
        $seller = \App\Models\Seller::first();
        $sellerId = $seller?->id;
        $brandId = Brand::where('name', 'Trendimus Originals')->first()?->id;

        $categories = Category::where('is_active', true)->pluck('id', 'name');

        $products = [
            // Necklaces
            ['name' => 'Pearl Layered Necklace', 'cat' => 'Necklaces & Pendants', 'price' => 1299, 'mrp' => 2499, 'desc' => 'Elegant multi-layered pearl necklace with anti-tarnish coating. Perfect for everyday wear and special occasions.', 'featured' => true],
            ['name' => 'Gold Plated Choker Set', 'cat' => 'Necklaces & Pendants', 'price' => 899, 'mrp' => 1799, 'desc' => 'Stunning gold-plated choker necklace set with matching earrings. Kundan work with anti-tarnish finish.', 'featured' => true],
            ['name' => 'Diamond Solitaire Pendant', 'cat' => 'Necklaces & Pendants', 'price' => 2499, 'mrp' => 4999, 'desc' => 'Classic solitaire pendant in 925 sterling silver with AAA cubic zirconia stone.', 'featured' => true],
            ['name' => 'Mangalsutra Modern Design', 'cat' => 'Necklaces & Pendants', 'price' => 1599, 'mrp' => 2999, 'desc' => 'Contemporary mangalsutra design with black beads and rose gold pendant.', 'featured' => false],
            ['name' => 'Temple Jewelry Necklace', 'cat' => 'Necklaces & Pendants', 'price' => 3499, 'mrp' => 6999, 'desc' => 'Traditional South Indian temple jewelry necklace with intricate antique gold plating.', 'featured' => true],

            // Earrings
            ['name' => 'Rose Gold Hoop Earrings', 'cat' => 'Earrings', 'price' => 599, 'mrp' => 1199, 'desc' => 'Minimalist rose gold hoops with anti-tarnish coating. Lightweight and comfortable for daily wear.', 'featured' => true],
            ['name' => 'Kundan Jhumka Earrings', 'cat' => 'Earrings', 'price' => 799, 'mrp' => 1499, 'desc' => 'Traditional kundan jhumka earrings with meenakari work and pearl drops.', 'featured' => true],
            ['name' => 'Diamond Studs 4mm', 'cat' => 'Earrings', 'price' => 1199, 'mrp' => 2399, 'desc' => 'Classic round diamond stud earrings in 925 silver. Sparkling AAA zirconia.', 'featured' => false],
            ['name' => 'Pearl Drop Chandbali', 'cat' => 'Earrings', 'price' => 999, 'mrp' => 1999, 'desc' => 'Elegant chandbali earrings with freshwater pearl drops and gold plating.', 'featured' => true],
            ['name' => 'Oxidised Silver Ear Cuff Set', 'cat' => 'Earrings', 'price' => 349, 'mrp' => 699, 'desc' => 'Trendy oxidised silver ear cuff set of 3. No piercing needed. Boho-chic style.', 'featured' => false],

            // Rings
            ['name' => 'Adjustable Butterfly Ring', 'cat' => 'Rings', 'price' => 399, 'mrp' => 799, 'desc' => 'Delicate butterfly ring with CZ stones. Adjustable size fits most fingers.', 'featured' => true],
            ['name' => 'Men\'s Signet Ring Gold', 'cat' => 'Rings', 'price' => 1499, 'mrp' => 2999, 'desc' => 'Bold gold-plated signet ring for men. Heavy weight with polished finish.', 'featured' => false],
            ['name' => 'Eternity Band Silver', 'cat' => 'Rings', 'price' => 899, 'mrp' => 1799, 'desc' => 'Full eternity band in 925 sterling silver with channel-set CZ stones.', 'featured' => true],

            // Bangles & Bracelets
            ['name' => 'Gold Plated Bangle Set (4pc)', 'cat' => 'Bangles & Bracelets', 'price' => 1199, 'mrp' => 2399, 'desc' => 'Set of 4 matching gold-plated bangles with intricate filigree work.', 'featured' => true],
            ['name' => 'Tennis Bracelet Rose Gold', 'cat' => 'Bangles & Bracelets', 'price' => 1799, 'mrp' => 3499, 'desc' => 'Luxurious tennis bracelet with round-cut CZ stones in rose gold setting.', 'featured' => true],
            ['name' => 'Charm Bracelet Silver', 'cat' => 'Bangles & Bracelets', 'price' => 699, 'mrp' => 1399, 'desc' => 'Adjustable charm bracelet with 5 removable charms. 925 sterling silver.', 'featured' => false],
            ['name' => 'Kada Bracelet Men', 'cat' => 'Bangles & Bracelets', 'price' => 999, 'mrp' => 1999, 'desc' => 'Thick gold-plated kada bracelet for men. Traditional Punjabi design.', 'featured' => false],

            // Nose Pins
            ['name' => 'Diamond Nose Pin Stud', 'cat' => 'Nose Pins & Rings', 'price' => 299, 'mrp' => 599, 'desc' => 'Tiny sparkling nose pin with genuine diamond-cut CZ stone. Screw-back for security.', 'featured' => true],
            ['name' => 'Gold Nose Ring Hoop', 'cat' => 'Nose Pins & Rings', 'price' => 249, 'mrp' => 499, 'desc' => 'Delicate 22K gold-plated nose ring hoop. Seamless design for comfort.', 'featured' => false],

            // Men's Jewelry
            ['name' => 'Cuban Link Chain 20"', 'cat' => 'Men\'s Jewellery', 'price' => 1999, 'mrp' => 3999, 'desc' => 'Heavy gold-plated Cuban link chain necklace 20 inches. Anti-tarnish coating.', 'featured' => true],
            ['name' => 'Black Onyx Cufflinks', 'cat' => 'Men\'s Jewellery', 'price' => 899, 'mrp' => 1799, 'desc' => 'Premium black onyx cufflinks in polished stainless steel setting.', 'featured' => false],

            // Wedding
            ['name' => 'Bridal Choker Set Complete', 'cat' => 'Wedding Jewellery', 'price' => 4999, 'mrp' => 9999, 'desc' => 'Complete bridal jewelry set: choker necklace, jhumka earrings, tikka, and bangles. Royal kundan work.', 'featured' => true],
            ['name' => 'Maang Tikka Pearl', 'cat' => 'Wedding Jewellery', 'price' => 599, 'mrp' => 1199, 'desc' => 'Elegant pearl maang tikka with gold plating. Perfect for bridal or festive wear.', 'featured' => false],

            // Kids
            ['name' => 'Kids Gold Plated Anklet', 'cat' => 'Kids Jewellery', 'price' => 199, 'mrp' => 399, 'desc' => 'Adorable gold-plated anklet for kids with tiny bell charms. Adjustable size.', 'featured' => false],
            ['name' => 'Baby Bracelet Silver', 'cat' => 'Kids Jewellery', 'price' => 349, 'mrp' => 699, 'desc' => 'Pure 925 silver baby bracelet with engraving option. Perfect gift for newborns.', 'featured' => false],

            // Silver
            ['name' => 'Silver Payal Set (Pair)', 'cat' => 'Silver Jewellery', 'price' => 1499, 'mrp' => 2999, 'desc' => 'Traditional silver payal anklet set with ghungroo bells. BIS hallmarked 925 silver.', 'featured' => true],
            ['name' => 'Oxidised Silver Pendant', 'cat' => 'Silver Jewellery', 'price' => 499, 'mrp' => 999, 'desc' => 'Handcrafted oxidised silver pendant with Ganesha motif. Comes with chain.', 'featured' => false],
        ];

        $count = 0;
        foreach ($products as $p) {
            $categoryId = null;
            foreach ($categories as $catName => $catId) {
                if (stripos($catName, explode(' ', $p['cat'])[0]) !== false || $catName === $p['cat']) {
                    $categoryId = $catId;
                    break;
                }
            }
            if (!$categoryId) {
                $categoryId = Category::where('name', 'like', '%' . explode(' ', $p['cat'])[0] . '%')
                    ->where('is_active', true)
                    ->first()?->id;
            }
            if (!$categoryId) continue;

            $product = Product::updateOrCreate(
                ['name' => $p['name']],
                [
                    'uuid' => (string) Str::uuid(),
                    'seller_id' => $sellerId,
                    'brand_id' => $brandId,
                    'category_id' => $categoryId,
                    'slug' => Str::slug($p['name']),
                    'short_description' => Str::limit($p['desc'], 100),
                    'description' => '<p>' . $p['desc'] . '</p><ul><li>Anti-tarnish coated</li><li>Hypoallergenic</li><li>Gift box included</li></ul>',
                    'sku' => 'MC-' . strtoupper(Str::random(6)),
                    'mrp' => $p['mrp'],
                    'price' => $p['price'],
                    'cost_price' => round($p['price'] * 0.4, 2),
                    'stock_quantity' => rand(10, 200),
                    'low_stock_threshold' => 5,
                    'stock_status' => 'in_stock',
                    'is_active' => true,
                    'is_featured' => $p['featured'],
                    'is_taxable' => true,
                    'tax_rate' => 3.00,
                    'rating' => round(rand(38, 49) / 10, 1),
                    'review_count' => rand(20, 350),
                    'view_count' => rand(100, 5000),
                    'sales_count' => rand(10, 500),
                    'status' => 'approved',
                    'published_at' => now()->subDays(rand(1, 60)),
                ]
            );
            $count++;
        }

        $this->command->info("Products seeded: {$count}");
    }

    private function seedReviews(): void
    {
        $names = [
            'Priya Sharma', 'Ritu Kapoor', 'Ananya Mehta', 'Deepika Nair', 'Kavita Joshi',
            'Sneha Patel', 'Pooja Verma', 'Meera Reddy', 'Anjali Gupta', 'Neha Singh',
            'Swati Agarwal', 'Divya Iyer', 'Sunita Rao', 'Komal Deshmukh', 'Shruti Bhat',
            'Aarti Mishra', 'Rashmi Kulkarni', 'Pallavi Shetty', 'Tanvi Chauhan', 'Nidhi Saxena',
            'Rohit Kumar', 'Arjun Menon', 'Vikram Thakur', 'Rahul Pandey', 'Karan Malhotra',
        ];

        $titles = [
            5 => ['Absolutely stunning!', 'Best purchase ever!', 'Exceeded my expectations!', 'Worth every penny!', 'Love it so much!', 'Perfect gift!', 'Amazing quality!'],
            4 => ['Very nice quality', 'Really happy with it', 'Good value for money', 'Looks great', 'Would recommend', 'Solid purchase'],
            3 => ['Decent product', 'Average quality', 'OK for the price', 'Could be better'],
        ];

        $contents = [
            5 => [
                'The quality is absolutely exceptional! The anti-tarnish coating is clearly working - still looks brand new after weeks of daily wear. Packaging was premium too.',
                'Ordered this as a gift for my sister and she was thrilled! The craftsmanship is beautiful and the design matches exactly what was shown in the pictures.',
                'I\'ve bought jewelry from many online stores but this is by far the best. The finish, the weight, everything feels premium. Will definitely order more!',
                'Received so many compliments wearing this! The rose gold color is gorgeous and the stones sparkle beautifully. Fast delivery too.',
                'This is my 3rd purchase from Trendimus and they never disappoint. The quality is consistent and the prices are very reasonable for what you get.',
                'Perfect for daily wear. Very comfortable, not too heavy. The anti-tarnish guarantee gives me confidence to wear it every day.',
            ],
            4 => [
                'Nice product overall. The design is elegant and the quality is good. Slightly smaller than I expected but still looks great.',
                'Happy with this purchase. Good packaging, quick delivery. The only reason I\'m not giving 5 stars is the clasp could be a bit sturdier.',
                'Beautiful piece of jewelry. My wife loved it as a birthday gift. Quality is much better than similarly priced items on other sites.',
                'Looks exactly like the photos. Good weight and finish. Would have liked a slightly longer chain but otherwise excellent.',
                'Very pretty and well made. The anti-tarnish coating seems solid. Wore it in the rain and no issues at all.',
            ],
            3 => [
                'Average product. Looks OK but not as shiny in person as in the photos. Decent for the price though.',
                'The design is nice but the quality could be improved. Some rough edges near the clasp. Still wearable.',
            ],
        ];

        $products = Product::where('is_active', true)->get();
        $count = 0;

        foreach ($products as $product) {
            // Skip if product already has reviews
            if ($product->reviews()->count() > 0) continue;

            $numReviews = rand(5, 15);
            for ($i = 0; $i < $numReviews; $i++) {
                $rating = $this->weightedRating();
                $name = $names[array_rand($names)];

                Review::create([
                    'product_id' => $product->id,
                    'guest_name' => $name,
                    'guest_email' => Str::slug($name, '.') . rand(1, 99) . '@gmail.com',
                    'rating' => $rating,
                    'title' => $titles[$rating][array_rand($titles[$rating])] ?? 'Good product',
                    'content' => $contents[$rating][array_rand($contents[$rating])] ?? 'Nice product, happy with purchase.',
                    'pros' => $this->randomPros(),
                    'cons' => $rating < 5 ? $this->randomCons() : [],
                    'is_verified_purchase' => rand(0, 1),
                    'is_approved' => true,
                    'is_featured' => $rating === 5 && rand(0, 3) === 0,
                    'helpful_count' => rand(0, 30),
                    'unhelpful_count' => rand(0, 3),
                    'status' => 'approved',
                    'is_generated' => true,
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);
                $count++;
            }

            // Update product rating and review count
            $avgRating = $product->reviews()->avg('rating');
            $reviewCount = $product->reviews()->count();
            $product->update([
                'rating' => round($avgRating, 1),
                'review_count' => $reviewCount,
            ]);
        }

        $this->command->info("Reviews seeded: {$count}");
    }

    private function weightedRating(): int
    {
        $rand = rand(1, 100);
        if ($rand <= 45) return 5;
        if ($rand <= 80) return 4;
        return 3;
    }

    private function randomPros(): array
    {
        $all = ['Great quality', 'Beautiful design', 'Anti-tarnish works', 'Good packaging', 'Fast delivery', 'Comfortable to wear', 'Looks expensive', 'True to pictures', 'Lightweight', 'Good value'];
        shuffle($all);
        return array_slice($all, 0, rand(2, 4));
    }

    private function randomCons(): array
    {
        $all = ['Slightly small', 'Could be shinier', 'Packaging could improve', 'Chain a bit thin'];
        shuffle($all);
        return array_slice($all, 0, rand(0, 1));
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            ['name' => 'Priya Sharma', 'title' => 'Regular Customer', 'content' => 'I\'ve been shopping at Trendimus for over a year now and every single piece has been amazing. The anti-tarnish guarantee is real - my first necklace still looks brand new! Best online jewelry store in India.', 'rating' => 5, 'product_name' => 'Pearl Layered Necklace'],
            ['name' => 'Ritu Kapoor', 'title' => 'Happy Bride', 'content' => 'Ordered my entire bridal set from Trendimus and received so many compliments on my wedding day. The quality rivals jewelry costing 3x more. Their customer service team was incredibly helpful with sizing.', 'rating' => 5, 'product_name' => 'Bridal Choker Set'],
            ['name' => 'Ananya Mehta', 'title' => 'Fashion Blogger', 'content' => 'As someone who reviews jewelry professionally, I can say Trendimus offers exceptional value. Their anti-tarnish coating is the best I\'ve tested, and the designs are trendy yet timeless.', 'rating' => 5, 'product_name' => 'Rose Gold Hoop Earrings'],
            ['name' => 'Deepika Nair', 'title' => 'Gift Buyer', 'content' => 'Gifted the pearl necklace to my mom on Mother\'s Day and she absolutely loved it! The premium gift packaging made it even more special. Will definitely shop here for all occasions.', 'rating' => 5, 'product_name' => 'Pearl Layered Necklace'],
            ['name' => 'Kavita Joshi', 'title' => 'Working Professional', 'content' => 'Finally found jewelry that survives my daily routine! I wear my Trendimus earrings and bracelet every day to work - through hand washing, rain, everything. No tarnish, no fading. Incredible.', 'rating' => 5, 'product_name' => 'Diamond Studs 4mm'],
            ['name' => 'Sneha Patel', 'title' => 'Repeat Customer', 'content' => 'This is my 5th order and every piece has been perfect. The mangalsutra I bought is my favourite - modern design that goes with both Indian and western outfits. Highly recommended!', 'rating' => 5, 'product_name' => 'Mangalsutra Modern Design'],
            ['name' => 'Meera Reddy', 'title' => 'College Student', 'content' => 'Love the affordable prices without compromising on quality! The oxidised silver collection is perfect for college wear. Plus the packaging is so pretty I can gift it straight away.', 'rating' => 4, 'product_name' => 'Oxidised Silver Pendant'],
            ['name' => 'Rohit Kumar', 'title' => 'Husband & Gifter', 'content' => 'Bought the tennis bracelet for my wife\'s anniversary and she was speechless! The quality exceeded both our expectations. The rose gold finish is stunning in person.', 'rating' => 5, 'product_name' => 'Tennis Bracelet Rose Gold'],
            ['name' => 'Anjali Gupta', 'title' => 'Verified Buyer', 'content' => 'The kundan jhumkas are absolutely gorgeous! Wore them to a friend\'s wedding and got asked multiple times where I bought them. Amazing craftsmanship at an unbelievable price.', 'rating' => 5, 'product_name' => 'Kundan Jhumka Earrings'],
            ['name' => 'Vikram Thakur', 'title' => 'Men\'s Collection Fan', 'content' => 'Finally an Indian brand that takes men\'s jewelry seriously! The Cuban link chain is heavy, well-made, and the gold plating is convincing. Gets compliments every time I wear it.', 'rating' => 5, 'product_name' => 'Cuban Link Chain 20"'],
        ];

        // Clear old testimonials
        Testimonial::truncate();

        foreach ($testimonials as $i => $t) {
            Testimonial::create(array_merge($t, [
                'position' => $i + 1,
                'is_active' => true,
            ]));
        }

        $this->command->info('Testimonials seeded: ' . count($testimonials));
    }

    private function seedBanners(): void
    {
        Banner::where('position', 'hero')->delete();

        $banners = [
            [
                'name' => 'Hero - Anti Tarnish Collection',
                'title' => 'Anti-Tarnish Jewelry',
                'subtitle' => 'Beauty that doesn\'t fade. Shop our premium collection.',
                'button_text' => 'Shop Now',
                'position' => 'hero',
                'image_url' => 'banners/hero-1.jpg',
                'link' => '/products',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Hero - Wedding Collection',
                'title' => 'Wedding Season',
                'subtitle' => 'Complete bridal sets starting at ₹4,999',
                'button_text' => 'Explore Bridal',
                'position' => 'hero',
                'image_url' => 'banners/hero-2.jpg',
                'link' => '/products?category=wedding-jewellery',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Hero - Flat 50% Off',
                'title' => 'Flat 50% Off',
                'subtitle' => 'On all earrings & necklaces this weekend',
                'button_text' => 'Shop Deals',
                'position' => 'hero',
                'image_url' => 'banners/hero-3.jpg',
                'link' => '/products?on_sale=1',
                'priority' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }

        $this->command->info('Banners seeded: ' . count($banners));
    }
}
