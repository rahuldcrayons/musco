<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::whereHas('admin')->first();

        $posts = [
            [
                'title'          => 'Top 10 Must-Have Home Essentials for 2025',
                'slug'           => 'top-10-must-have-home-essentials-2025',
                'category'       => 'Home & Living',
                'tags'           => ['home essentials', 'kitchen', 'organization', 'smart home'],
                'featured_image' => 'blog/blog-home-essentials.jpg',
                'excerpt'        => 'Transform your living space with these 10 essential products that combine functionality with style. From smart kitchen gadgets to clever storage solutions, elevate your everyday life.',
                'content'        => '<h2>Why Home Essentials Matter</h2><p>Your home environment directly affects your mood, productivity, and well-being. The right products can save you time, reduce stress, and make daily routines more enjoyable. Here are our top picks for 2025.</p><h2>1. Multi-functional Kitchen Organizer</h2><p>A well-organized kitchen is the heart of every home. Invest in modular kitchen organizers that maximize counter and cabinet space. Look for stackable designs with non-slip bases.</p><h2>2. Smart LED Desk Lamp</h2><p>Whether you work from home or study late, a good desk lamp reduces eye strain. Choose one with adjustable color temperature (warm to cool white) and brightness levels.</p><h2>3. Premium Bedsheet Set</h2><p>Quality sleep starts with quality bedding. Cotton or bamboo-blend sheets with a thread count of 300+ feel luxurious and regulate temperature throughout the night.</p><h2>4. Compact Air Purifier</h2><p>With rising pollution levels, an air purifier is no longer a luxury — it is a necessity. HEPA filters capture 99.97% of particles, including dust, pollen, and PM2.5.</p><h2>5. Reusable Storage Containers</h2><p>Replace single-use plastic with glass or BPA-free containers. Airtight lids keep food fresh longer and they are microwave and dishwasher safe.</p><h2>6. Wall-Mounted Shelves</h2><p>Floating shelves add storage without eating floor space. Use them for books, plants, or decorative items to add personality to any room.</p><h2>7. Non-Stick Cookware Set</h2><p>A good set of non-stick pans makes cooking healthier (less oil needed) and cleanup easier. Look for PFOA-free coatings and induction-compatible bases.</p><h2>8. Bathroom Caddy Organizer</h2><p>Keep toiletries tidy with a rust-proof shower caddy. Stainless steel or bamboo options look great and last for years.</p><h2>9. Indoor Plants with Self-Watering Pots</h2><p>Plants improve air quality and mental health. Self-watering pots are perfect for busy people who forget to water their green friends.</p><h2>10. Multi-Port USB Charging Station</h2><p>Consolidate all your device chargers into one neat station. Reduces cable clutter and keeps phones, tablets, and earbuds charged and accessible.</p><blockquote>Small upgrades to your home environment can make a big difference in your daily quality of life. Start with one or two items and build from there.</blockquote>',
                'is_published'   => true,
                'published_at'   => now()->subDays(12),
                'view_count'     => 347,
                'seo_data'       => [
                    'meta_title'       => 'Top 10 Must-Have Home Essentials for 2025 | Jikra Blog',
                    'meta_description' => 'Discover the top 10 home essentials for 2025 — from smart kitchen gadgets to bedroom upgrades. Transform your living space today.',
                ],
            ],
            [
                'title'          => 'How to Choose the Right Personal Care Products for Your Skin Type',
                'slug'           => 'choose-right-personal-care-products-skin-type',
                'category'       => 'Personal Care',
                'tags'           => ['skincare', 'personal care', 'beauty', 'skin type'],
                'featured_image' => 'blog/blog-personal-care.jpg',
                'excerpt'        => 'Not all skincare products work for everyone. Learn how to identify your skin type and choose products that actually deliver results — without the trial-and-error frustration.',
                'content'        => '<h2>Know Your Skin Type First</h2><p>Before buying any product, identify your skin type: oily, dry, combination, or sensitive. The simplest test? Wash your face, wait an hour without applying anything, then check. Shiny all over = oily. Tight and flaky = dry. Shiny T-zone with dry cheeks = combination.</p><h2>For Oily Skin</h2><p>Look for lightweight, water-based, non-comedogenic products. Gel cleansers, oil-free moisturizers, and niacinamide serums help control excess sebum without over-drying.</p><h2>For Dry Skin</h2><p>Choose cream-based cleansers and rich moisturizers with hyaluronic acid, ceramides, or shea butter. Avoid products with alcohol or strong fragrances that strip moisture.</p><h2>For Combination Skin</h2><p>You may need different products for different zones. A lightweight moisturizer for the T-zone and a richer one for cheeks works well. Multi-zone masking is also effective.</p><h2>For Sensitive Skin</h2><p>Stick to fragrance-free, hypoallergenic products with minimal ingredients. Patch test everything on your inner arm for 24 hours before applying to your face.</p><h2>The Non-Negotiables</h2><p>Regardless of skin type, everyone needs three things: a good cleanser, a moisturizer, and sunscreen (SPF 30+). Everything else — serums, toners, masks — is a bonus.</p><blockquote>The best skincare routine is one you will actually follow consistently. Keep it simple, be patient, and give products at least 4-6 weeks to show results.</blockquote>',
                'is_published'   => true,
                'published_at'   => now()->subDays(8),
                'view_count'     => 218,
                'seo_data'       => [
                    'meta_title'       => 'How to Choose Personal Care Products for Your Skin Type | Jikra',
                    'meta_description' => 'Learn to identify your skin type and pick the right personal care products. Expert tips for oily, dry, combination, and sensitive skin.',
                ],
            ],
            [
                'title'          => 'Smart Shopping: 7 Tips to Save Money Online Without Compromising Quality',
                'slug'           => 'smart-shopping-tips-save-money-online',
                'category'       => 'Shopping Tips',
                'tags'           => ['online shopping', 'savings', 'deals', 'coupons'],
                'featured_image' => 'blog/blog-smart-shopping.jpg',
                'excerpt'        => 'Shopping smart does not mean buying cheap. These 7 proven strategies help you find the best deals, use coupons effectively, and get more value from every rupee spent online.',
                'content'        => '<h2>1. Compare Before You Buy</h2><p>Never buy the first option you see. Spend 5 minutes comparing prices across platforms. Price differences of 15-30% for the same product are common.</p><h2>2. Use Coupon Codes</h2><p>Most e-commerce stores offer coupon codes for first-time buyers or during sale events. At Jikra, use code <strong>WELCOME20</strong> for 20% off your first order. Always check for available coupons before checkout.</p><h2>3. Buy During Sale Seasons</h2><p>Plan big purchases around major sale events like Diwali, Republic Day, and end-of-season sales. Discounts of 40-70% are common during these periods.</p><h2>4. Read Reviews Carefully</h2><p>Look for reviews with photos and detailed descriptions. Focus on 3-4 star reviews — they tend to be the most honest and balanced. Be wary of products with only 5-star reviews.</p><h2>5. Check Return Policies</h2><p>Before buying, always check the return and exchange policy. A good return policy (like Jikra\'s 7-day hassle-free returns) protects you from bad purchases.</p><h2>6. Sign Up for Newsletters</h2><p>Many stores send exclusive discount codes to email subscribers. It takes 30 seconds to sign up and could save you hundreds over the year.</p><h2>7. Bundle Products When Possible</h2><p>Buying combos or bundled products often costs 20-30% less than buying items individually. This is especially true for personal care, kitchen, and home essentials.</p><blockquote>The smartest shoppers are not the ones who spend the least — they are the ones who get the most value from every purchase.</blockquote>',
                'is_published'   => true,
                'published_at'   => now()->subDays(5),
                'view_count'     => 512,
                'seo_data'       => [
                    'meta_title'       => '7 Smart Shopping Tips to Save Money Online | Jikra Blog',
                    'meta_description' => 'Learn 7 proven strategies to save money shopping online without compromising quality. Coupon tips, sale timing, and smart comparison shopping.',
                ],
            ],
            [
                'title'          => 'The Complete Guide to Gifting: Perfect Presents for Every Occasion',
                'slug'           => 'complete-guide-gifting-perfect-presents',
                'category'       => 'Gifting',
                'tags'           => ['gifts', 'gifting ideas', 'birthday', 'anniversary'],
                'featured_image' => 'blog/blog-gifting-guide.jpg',
                'excerpt'        => 'Struggling to find the perfect gift? This guide covers thoughtful gift ideas for birthdays, anniversaries, festivals, and housewarmings — at every budget from ₹299 to ₹2999.',
                'content'        => '<h2>The Art of Thoughtful Gifting</h2><p>A great gift shows you know and care about the person. It does not have to be expensive — it has to be relevant. The best gifts solve a problem, match a hobby, or create a memorable experience.</p><h2>Under ₹500: Practical & Thoughtful</h2><p>Scented candles, premium notebooks, phone stands, reusable water bottles, or desk organizers. These are everyday items people love but rarely buy for themselves.</p><h2>₹500 – ₹1000: Elevated Everyday</h2><p>Skincare gift sets, premium kitchen tools, Bluetooth speakers, wireless earbuds, or beautiful planters with indoor plants. These feel special without breaking the bank.</p><h2>₹1000 – ₹2000: Premium Touch</h2><p>Smart gadgets, luxury personal care hampers, quality bedding sets, or fitness accessories. These gifts feel luxurious and show genuine consideration.</p><h2>₹2000+: Statement Gifts</h2><p>High-quality appliances, curated gift boxes, premium electronics, or experience vouchers. For those occasions when you want to go the extra mile.</p><h2>Festival-Specific Ideas</h2><p><strong>Diwali:</strong> Decorative items, dry fruit boxes, scented candle sets.<br><strong>Rakhi:</strong> Grooming kits, watches, personalized items.<br><strong>Birthdays:</strong> Based on the person\'s hobbies and interests — always the safest bet.</p><blockquote>When in doubt, a beautifully packaged practical gift always wins over an expensive but impersonal one.</blockquote>',
                'is_published'   => true,
                'published_at'   => now()->subDays(3),
                'view_count'     => 289,
                'seo_data'       => [
                    'meta_title'       => 'Complete Gifting Guide: Perfect Presents for Every Occasion | Jikra',
                    'meta_description' => 'Find the perfect gift for any occasion and budget. Thoughtful ideas for birthdays, festivals, anniversaries, and more.',
                ],
            ],
            [
                'title'          => 'Why Quality Products Matter: A Guide to Buying Smart',
                'slug'           => 'why-quality-products-matter-buying-smart',
                'category'       => 'Shopping Tips',
                'tags'           => ['quality', 'value', 'brand', 'buying guide'],
                'featured_image' => 'blog/blog-quality-matters.jpg',
                'excerpt'        => 'Cheap products often cost more in the long run. Learn why investing in quality pays off, how to spot well-made products, and when to splurge vs save.',
                'content'        => '<h2>The True Cost of Cheap Products</h2><p>That ₹99 phone charger that broke in a week? Or the ₹199 earphones that stopped working in a month? When you factor in replacements, cheap products often cost 2-3x more than buying quality once.</p><h2>How to Spot Quality</h2><p><strong>Materials:</strong> Check what the product is made of. Stainless steel over plastic, cotton over polyester, tempered glass over regular glass.<br><strong>Reviews:</strong> Products with consistent 4+ star ratings across hundreds of reviews are usually reliable.<br><strong>Brand reputation:</strong> Established brands stake their reputation on quality. At Jikra, we vet every product before listing it.</p><h2>Where to Splurge</h2><p>Invest more in items you use daily: kitchen cookware, bedsheets, phone accessories, skincare basics, and footwear. The per-use cost of a ₹1500 item used for 3 years is lower than a ₹300 item replaced every 3 months.</p><h2>Where to Save</h2><p>Seasonal items, trendy accessories, decorative pieces, and trial-size products are fine to buy at lower price points. If you are trying something new, start affordable.</p><h2>The Jikra Quality Promise</h2><p>Every product on Jikra goes through a quality check. We source from verified brands and authorized distributors. Our 7-day return policy ensures that if something does not meet your expectations, you are always protected.</p><blockquote>Buy it nice or buy it twice. Quality is not about spending more — it is about spending wisely.</blockquote>',
                'is_published'   => true,
                'published_at'   => now()->subDays(1),
                'view_count'     => 176,
                'seo_data'       => [
                    'meta_title'       => 'Why Quality Products Matter: A Buying Guide | Jikra Blog',
                    'meta_description' => 'Learn why investing in quality products saves money long-term. Tips to spot well-made products and when to splurge vs save.',
                ],
            ],
        ];

        foreach ($posts as $data) {
            $data['author_id'] = $author?->id;
            BlogPost::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info('Blog posts seeded: ' . count($posts) . ' posts (all published)');
    }
}
