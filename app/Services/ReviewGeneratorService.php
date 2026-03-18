<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;

class ReviewGeneratorService
{
    public function generateForOrderItem(OrderItem $orderItem): Review
    {
        $product = $orderItem->product;
        $order = $orderItem->order;
        $user = $order->user;
        $rating = $this->pickRating();

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_item_id' => $orderItem->id,
            'rating' => $rating,
            'title' => $this->generateTitle($product, $rating),
            'content' => $this->generateContent($product, $rating),
            'pros' => $this->generatePros($product, $rating),
            'cons' => $this->generateCons($product, $rating),
            'is_verified_purchase' => true,
            'is_approved' => true,
            'status' => 'approved',
            'is_generated' => true,
            'generated_from_order_item_id' => $orderItem->id,
            'helpful_count' => rand(0, 8),
            'created_at' => $this->randomTimestampToday(),
        ]);

        return $review;
    }

    public function pickRating(): int
    {
        $rand = mt_rand(1, 100);

        return match (true) {
            $rand <= 1 => 1,    // 1%
            $rand <= 5 => 2,    // 4%
            $rand <= 20 => 3,   // 15%
            $rand <= 55 => 4,   // 35%
            default => 5,       // 45%
        };
    }

    public function generateTitle(Product $product, int $rating): string
    {
        $categorySlug = $product->category?->slug ?? '';
        $productType = $this->detectProductType($product->name, $categorySlug);

        $templates = $this->getTitleTemplates($rating);
        $template = $templates[array_rand($templates)];

        return $this->fillPlaceholders($template, $product, $productType);
    }

    public function generateContent(Product $product, int $rating): string
    {
        $categorySlug = $product->category?->slug ?? '';
        $productType = $this->detectProductType($product->name, $categorySlug);
        $timeframe = $this->randomTimeframe();

        $templates = $this->getContentTemplates($rating);
        $template = $templates[array_rand($templates)];

        return $this->fillPlaceholders($template, $product, $productType, $timeframe);
    }

    public function generatePros(Product $product, int $rating): array
    {
        $categorySlug = $product->category?->slug ?? '';
        $pool = $this->getProsPool($categorySlug);
        $count = match (true) {
            $rating >= 5 => rand(3, 4),
            $rating >= 4 => rand(2, 3),
            $rating >= 3 => rand(1, 2),
            default => rand(0, 1),
        };

        shuffle($pool);
        return array_slice($pool, 0, $count);
    }

    public function generateCons(Product $product, int $rating): array
    {
        if ($rating >= 5 && rand(1, 3) > 1) {
            return [];
        }

        $pool = $this->getConsPool();
        $count = match (true) {
            $rating <= 2 => rand(2, 3),
            $rating <= 3 => rand(1, 2),
            $rating <= 4 => rand(0, 1),
            default => 0,
        };

        shuffle($pool);
        return array_slice($pool, 0, $count);
    }

    private function randomTimestampToday(): \Carbon\Carbon
    {
        $hour = rand(6, 22);
        $minute = rand(0, 59);
        $second = rand(0, 59);

        return now()->setTime($hour, $minute, $second);
    }

    private function randomTimeframe(): string
    {
        $frames = ['a few days', 'a week', 'about two weeks', 'a couple of weeks', 'some time now', 'a month'];
        return $frames[array_rand($frames)];
    }

    private function detectProductType(string $productName, string $categorySlug): string
    {
        $name = strtolower($productName . ' ' . $categorySlug);

        if (preg_match('/earphone|earbuds|earbud|tws|headphone|headset|neckband/', $name)) return 'earphones';
        if (preg_match('/speaker|soundbar|sound bar/', $name)) return 'speaker';
        if (preg_match('/charger|adapter|charging/', $name)) return 'charger';
        if (preg_match('/cable|cord|wire|usb/', $name)) return 'cable';
        if (preg_match('/power.?bank|portable.?charger/', $name)) return 'power bank';
        if (preg_match('/case|cover|back.?cover|bumper|flip/', $name)) return 'phone case';
        if (preg_match('/tempered|screen.?guard|screen.?protector|glass/', $name)) return 'screen protector';
        if (preg_match('/smartwatch|smart.?watch|fitness.?band|watch/', $name)) return 'smartwatch';
        if (preg_match('/mount|holder|stand|tripod|selfie/', $name)) return 'phone mount';
        if (preg_match('/gaming|controller|trigger|cool/', $name)) return 'gaming accessory';
        if (preg_match('/memory|sd.?card|otg|hub|adapter/', $name)) return 'adapter';
        if (preg_match('/ring.?holder|pop.?socket|grip/', $name)) return 'phone grip';

        return 'product';
    }

    private function fillPlaceholders(string $template, Product $product, string $productType, string $timeframe = ''): string
    {
        $shortName = strlen($product->name) > 40 ? substr($product->name, 0, 37) . '...' : $product->name;

        return str_replace(
            ['{product_name}', '{product_type}', '{timeframe}', '{brand}'],
            [$shortName, $productType, $timeframe, $product->brand?->name ?? 'this brand'],
            $template
        );
    }

    // ─── Title Templates ─────────────────────────────────────

    private function getTitleTemplates(int $rating): array
    {
        return match (true) {
            $rating >= 5 => [
                'Absolutely love this {product_type}!',
                'Best {product_type} at this price',
                'Exceeded all my expectations',
                'Best purchase this month!',
                'Amazing quality {product_type}',
                'Worth every rupee',
                'Highly recommend this',
                'Fantastic {product_type}!',
                'So happy with this purchase',
                'Great value for money',
                'Beautiful {product_type}, very pleased',
                'Wonderful quality and build',
                'Outstanding {product_type}!',
                'Must buy! Really impressed',
                'Exactly what I was looking for',
                'Superb quality {product_type}',
                'Delighted with this purchase',
                'Simply the best {product_type}',
                'Brilliant quality and finish',
                '5 stars all the way!',
                'Very happy customer!',
                'Top quality {product_type}',
                'Impressed with the quality',
                'Perfect for daily use',
                'Works flawlessly!',
                'Premium feel at budget price',
                'Better than branded alternatives',
                'Using it daily, no complaints',
            ],
            $rating >= 4 => [
                'Good quality {product_type}',
                'Happy with this purchase',
                'Nice {product_type}, minor things',
                'Pretty good for the price',
                'Solid {product_type}, would recommend',
                'Good value, decent quality',
                'Satisfied with this {product_type}',
                'Mostly impressed, small issue',
                'Nice design and build',
                'Good {product_type}, works well',
                'Decent quality, looks great',
                'Happy overall with this',
                'Good product, quick delivery',
                'Like it, but small improvements needed',
                'A solid purchase overall',
                'Quite nice {product_type}',
                'Pleased with the quality',
                'Good for everyday use',
                'Reasonably good {product_type}',
                'Better than expected',
                'Would buy again',
                'Good performance for the price',
                'Reliable {product_type}',
                'Does the job well',
            ],
            $rating >= 3 => [
                'Decent but could be better',
                'OK for the price',
                'Average {product_type}',
                'It does the job',
                'Mixed feelings about this',
                'Some pros and cons',
                'Fair quality for the price',
                'Acceptable {product_type}',
                'Not bad, not great either',
                'Meets basic expectations',
                'Could use some improvements',
                'Average quality {product_type}',
                'Somewhat satisfied',
                'Decent product overall',
                'Works fine, nothing special',
            ],
            $rating >= 2 => [
                'Disappointed with the quality',
                'Not as expected',
                'Would not buy again',
                'Below average {product_type}',
                'Not worth the price',
                'Quality could be much better',
                'Expected better for the price',
                'Underwhelming {product_type}',
                'Not satisfied',
                'Did not meet expectations',
            ],
            default => [
                'Very disappointing',
                'Not recommended',
                'Poor quality {product_type}',
                'Waste of money',
                'Would not recommend',
                'Extremely disappointed',
                'Bad quality overall',
            ],
        };
    }

    // ─── Content Templates ───────────────────────────────────

    private function getContentTemplates(int $rating): array
    {
        return match (true) {
            $rating >= 5 => [
                'Bought this {product_type} and it turned out to be a great purchase. The build quality is excellent and it feels premium. Really happy with how it performs.',
                'I have been looking for a good {product_type} for {timeframe} and finally found this one. Works perfectly and the quality is top notch for the price.',
                'This {product_type} is amazing! I have been using it for {timeframe} now and it still works like new. Definitely worth the money.',
                'Excellent product! The {product_type} works perfectly and feels very well built. Will definitely order more from Jikra.',
                'Very impressed with this {product_type}. It is exactly as shown in the pictures. Fast shipping too! No complaints at all.',
                'This is my third purchase from Jikra and every time the quality has been consistent. This {product_type} is no exception. Great build, great performance.',
                'I was skeptical about ordering this online but this {product_type} changed my mind. The quality is outstanding for this price range. Highly recommend.',
                'Just received this {product_type} and I am thoroughly impressed. The attention to detail is wonderful. Would definitely order from Jikra again.',
                'Fantastic quality for the price! The {product_type} is well-made and performs great. Very happy with this find.',
                'This {product_type} is honestly one of the best I have bought in this price range. Great build, works perfectly, and looks premium.',
                'Was looking for something reliable and this {product_type} did not disappoint. Everyone I have shown it to is impressed. Great quality and great price.',
                'Purchased this {product_type} after reading other reviews and I am glad I did. I have been using it constantly. The quality is exceptional for what you pay.',
                'Absolutely love this {product_type}! I use it every day and it has not let me down once. The build quality feels premium.',
                'I compared this with several other options and this {product_type} stood out for the quality. Been using it for {timeframe} and it still looks and works amazing.',
                'Really pleased with this purchase. The {product_type} is well-built and performs flawlessly. Good experience shopping here.',
                'Wonderful {product_type}! Got so many compliments on it. Works great and the quality is really impressive for this price.',
                'Using this {product_type} for {timeframe} now. No issues whatsoever. Sound quality, build quality, everything is top notch. Best value for money.',
                'Ordered this for myself and was so impressed that I bought another one as a gift. The {product_type} performs brilliantly. Jikra has earned a loyal customer.',
                'The {product_type} arrived well packaged and works perfectly out of the box. Setup was easy and performance has been flawless for {timeframe} now.',
                'Hands down the best {product_type} I have used at this price point. {brand} has really delivered on quality. Will recommend to everyone.',
            ],
            $rating >= 4 => [
                'Good {product_type} overall. It works well and the quality is decent for the price. Only minor thing is the build could be a tiny bit sturdier.',
                'Bought this and it is a solid purchase. Nice build and good performance. Could be slightly better in terms of packaging.',
                'Happy with this {product_type}. Been using it for {timeframe}. The quality is good though I wish the finish was a bit more premium.',
                'Pretty good {product_type} for the price point. It works exactly as described. Delivery was prompt. Would have given 5 stars if packaging was better.',
                'Nice {product_type}! Works well for daily use. The quality is better than what I expected at this price range.',
                'Decent purchase. I use this {product_type} daily. It is well-made and performs reliably. Minor issue with the cable length but overall satisfied.',
                'This {product_type} is quite nice. I use it regularly. Good quality and value for money. Shipping was fast. Would recommend with minor reservations.',
                'I like this {product_type} a lot. The build feels good and it works exactly like the pictures show. Just wish it came in more colors.',
                'Solid {product_type} from Jikra. I have been using it for {timeframe}. Good quality but took a bit longer to deliver than expected.',
                'Good buy! The {product_type} works well and looks great. One small thing - it could have come with better accessories.',
                'Overall a good {product_type}. Quality is good for the price. Just needs slightly better packaging for delivery to prevent scratches.',
                'Nice {product_type} from Jikra. Performance is good and the design looks premium. Value for money. Would buy again.',
                'The {product_type} from {brand} is really nice. Performs well for daily use. Only giving 4 stars because the charging speed could be faster.',
                'Good product for the price. The {product_type} has been working great for {timeframe}. Build quality is solid. Recommended.',
                'Satisfied with this {product_type}. It does what it promises. Minor improvements in build quality would make it perfect.',
            ],
            $rating >= 3 => [
                'The {product_type} is okay. Not bad but not exceptional either. It works but the build quality could be better for what I paid.',
                'Average {product_type}. It serves its purpose. The design is fine but the materials feel a bit cheap.',
                'Mixed feelings about this one. The design looks nice but the quality does not feel premium. Fair for the price I guess.',
                'It is alright. The {product_type} works but the performance is inconsistent sometimes. Okay product overall.',
                'Decent {product_type} for basic use. I have had it for {timeframe} now. The build could be better but it is acceptable for the price.',
                'Got this and it is average quality. The product photos look better than reality. Not bad but I expected more.',
                'The {product_type} is fine for everyday use. Quality is standard, nothing to write home about.',
                'OK product. I use it but it is not my favourite. The build is acceptable and the performance is basic. Meets basic expectations.',
                'Average purchase. The {product_type} looks decent but the quality is just okay. Fair value for what you pay.',
                'Not great, not terrible. This {product_type} does the job. Been using it for {timeframe}. Some quality issues but still works.',
            ],
            $rating >= 2 => [
                'Honestly expected better. The {product_type} quality is below what I thought I would get. The materials feel cheap. Not worth the price.',
                'The {product_type} looked much better in the photos. In person, the quality is disappointing. Started having issues within a week.',
                'Not happy with this purchase. The build quality is poor. Would not recommend at this price.',
                'Below average {product_type}. Started malfunctioning after a few days. Quality control seems lacking.',
                'Disappointed with the quality of this {product_type}. For the price paid, I expected much better. The build is flimsy.',
                'Not worth it. The {product_type} quality is poor and does not match the product images at all. Considering returning this.',
            ],
            default => [
                'Very poor quality {product_type}. The build feels terrible and it stopped working within days. Completely different from what was shown online. Very disappointed.',
                'Terrible purchase. The {product_type} arrived with defects and barely works. Complete waste of money. Would not recommend to anyone.',
                'Worst {product_type} I have bought. Cheap materials, poor build, and nothing like the pictures. Regret buying this.',
            ],
        };
    }

    // ─── Pros/Cons Pools ─────────────────────────────────────

    private function getProsPool(string $categorySlug): array
    {
        $general = [
            'Good build quality',
            'Fast delivery',
            'Nice packaging',
            'Value for money',
            'True to description',
            'Looks premium',
            'Easy to use',
            'Good colour options',
            'Durable build',
            'Great for daily use',
        ];

        $category = strtolower($categorySlug);

        if (str_contains($category, 'earphone') || str_contains($category, 'headphone') || str_contains($category, 'earbud') || str_contains($category, 'neckband') || str_contains($category, 'tws')) {
            return array_merge($general, [
                'Clear sound quality',
                'Deep bass',
                'Comfortable fit',
                'Good noise cancellation',
                'Long battery life',
                'Fast charging',
                'No connectivity drops',
                'Good mic for calls',
                'Lightweight and compact',
                'Low latency gaming mode',
            ]);
        }

        if (str_contains($category, 'speaker') || str_contains($category, 'bluetooth')) {
            return array_merge($general, [
                'Loud and clear sound',
                'Punchy bass',
                'Long battery life',
                'Portable and lightweight',
                'Water resistant',
                'Pairs quickly via Bluetooth',
                'Good range',
                'Solid build quality',
                'TWS pairing works well',
                'Great for outdoor use',
            ]);
        }

        if (str_contains($category, 'charger') || str_contains($category, 'cable')) {
            return array_merge($general, [
                'Charges fast',
                'Sturdy cable build',
                'No overheating',
                'Compact design',
                'Works with all devices',
                'Braided cable lasts long',
                'Multiple port support',
                'USB-C compatibility',
                'Good cable length',
                'BIS certified',
            ]);
        }

        if (str_contains($category, 'power') || str_contains($category, 'bank')) {
            return array_merge($general, [
                'Fast charging output',
                'Charges phone fully 2-3 times',
                'Slim and portable',
                'LED indicator is helpful',
                'USB-C input charges quickly',
                'Good capacity for travel',
                'Charges multiple devices',
                'Solid build, no flex',
                'Safe with overcharge protection',
                'Great for long trips',
            ]);
        }

        if (str_contains($category, 'case') || str_contains($category, 'cover') || str_contains($category, 'screen') || str_contains($category, 'protector') || str_contains($category, 'tempered')) {
            return array_merge($general, [
                'Perfect fit for phone model',
                'Good drop protection',
                'Buttons are clickable through case',
                'Camera cutout is precise',
                'Does not add much bulk',
                'No yellowing so far',
                'Anti-fingerprint finish',
                'Easy to install',
                'Raised edges protect screen',
                'Clear and transparent',
            ]);
        }

        if (str_contains($category, 'smartwatch') || str_contains($category, 'watch') || str_contains($category, 'band') || str_contains($category, 'fitness')) {
            return array_merge($general, [
                'AMOLED display is bright',
                'Bluetooth calling works well',
                'Accurate heart rate tracking',
                'Battery lasts 5-6 days',
                'Comfortable to wear all day',
                'Many watch face options',
                'SpO2 monitoring is useful',
                'Quick Bluetooth pairing',
                'Water resistant for workouts',
                'Sleep tracking is accurate',
            ]);
        }

        if (str_contains($category, 'gaming') || str_contains($category, 'trigger') || str_contains($category, 'controller') || str_contains($category, 'cooling')) {
            return array_merge($general, [
                'Improved my gaming performance',
                'Responsive trigger buttons',
                'Phone stays cool during gaming',
                'Low latency connection',
                'Comfortable grip',
                'Easy to attach and remove',
                'Works great for BGMI',
                'Compact and portable',
                'Good build for the price',
                'Noticeable difference in gameplay',
            ]);
        }

        if (str_contains($category, 'mount') || str_contains($category, 'stand') || str_contains($category, 'holder') || str_contains($category, 'selfie') || str_contains($category, 'tripod')) {
            return array_merge($general, [
                'Strong grip, phone stays secure',
                '360 degree rotation works smoothly',
                'Easy to install in car',
                'Sturdy on desk',
                'Adjustable angle is useful',
                'Does not block AC vent',
                'Compact when folded',
                'Works with all phone sizes',
                'Good suction, stays in place',
                'Perfect for video calls',
            ]);
        }

        return $general;
    }

    private function getConsPool(): array
    {
        return [
            'Packaging could be better',
            'Delivery took a bit longer',
            'Colour slightly different from photo',
            'Instructions not very clear',
            'Limited colour choices',
            'Price is a touch high',
            'Could include better accessories',
            'Build feels slightly plasticky',
            'Cable could be longer',
            'Wish it came with a carry pouch',
            'Gets warm with extended use',
            'Button placement could be better',
            'Slightly heavier than expected',
            'Would prefer better packaging',
        ];
    }
}
