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
        $productType = $this->detectProductType($product);

        $templates = $this->getTitleTemplates($rating);
        $template = $templates[array_rand($templates)];

        return $this->fillPlaceholders($template, $product, $productType);
    }

    public function generateContent(Product $product, int $rating): string
    {
        $productType = $this->detectProductType($product);
        $timeframe = $this->randomTimeframe();

        $templates = $this->getContentTemplates($rating);
        $template = $templates[array_rand($templates)];

        return $this->fillPlaceholders($template, $product, $productType, $timeframe);
    }

    public function generatePros(Product $product, int $rating): array
    {
        $productType = $this->detectProductType($product);
        $pool = $this->getProsPool($productType);
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
        return now()->setTime(rand(6, 22), rand(0, 59), rand(0, 59));
    }

    private function randomTimeframe(): string
    {
        $frames = ['a few days', 'a week', 'about two weeks', 'a couple of weeks', 'some time now', 'a month'];
        return $frames[array_rand($frames)];
    }

    /**
     * Detect product type from product name + category name for template matching.
     */
    private function detectProductType(Product $product): string
    {
        $name = strtolower($product->name);
        $cat = strtolower($product->category?->name ?? '');
        $text = $name . ' ' . $cat;

        // Audio
        if (preg_match('/earphone|earbuds|earbud|tws|headphone|headset|neckband|airpod/', $text)) return 'earphones';
        if (preg_match('/speaker|soundbar|sound.?bar/', $text)) return 'speaker';

        // Charging & Power
        if (preg_match('/charger|charging.?adapter/', $text)) return 'charger';
        if (preg_match('/cable|cord|usb|lightning|type.?c/', $text)) return 'cable';
        if (preg_match('/power.?bank|portable.?charger/', $text)) return 'power bank';

        // Phone protection
        if (preg_match('/case|cover|back.?cover|bumper|flip.?cover/', $text)) return 'phone case';
        if (preg_match('/tempered|screen.?guard|screen.?protector/', $text)) return 'screen protector';

        // Wearables
        if (preg_match('/smart.?watch|fitness.?band|smart.?bracelet/', $text)) return 'smartwatch';
        if (preg_match('/watch.?strap|watch.?band/', $text)) return 'watch strap';

        // Mounts & stands
        if (preg_match('/selfie|tripod|ring.?light/', $text)) return 'selfie stick';
        if (preg_match('/car.?mount|car.?holder|phone.?holder|dash/', $text)) return 'car mount';
        if (preg_match('/mount|stand|holder|dock/', $text)) return 'stand';

        // Gaming
        if (preg_match('/gaming|controller|trigger|gamepad/', $text)) return 'gaming accessory';
        if (preg_match('/cooling.?fan|phone.?cooler/', $text)) return 'cooling fan';

        // Kitchen & Dining
        if (preg_match('/chopper|blender|grinder|mixer|processor|juicer/', $text)) return 'kitchen appliance';
        if (preg_match('/bottle|flask|tumbler|sipper/', $text)) return 'bottle';
        if (preg_match('/container|lunch|tiffin|storage.?box/', $text)) return 'container';
        if (preg_match('/kitchen|utensil|cook|pan|pot|knife|cutting|peeler/', $text)) return 'kitchen tool';
        if (preg_match('/mug|cup|glass|plate|bowl/', $text)) return 'kitchenware';

        // Household Supplies
        if (preg_match('/hook|hanger|adhesive|rack|shelf|organiz/', $text)) return 'organizer';
        if (preg_match('/clean|brush|mop|broom|duster|wipe/', $text)) return 'cleaning tool';
        if (preg_match('/soap|dispenser|holder|bathroom|shower|toilet/', $text)) return 'bathroom accessory';
        if (preg_match('/cloth.?line|laundry|dryer|iron/', $text)) return 'laundry tool';
        if (preg_match('/tape|glue|seal|repair|tool/', $text)) return 'household tool';
        if (preg_match('/bin|trash|dustbin|garbage/', $text)) return 'dustbin';
        if (preg_match('/mat|rug|carpet|doormat/', $text)) return 'mat';

        // Lighting
        if (preg_match('/lamp|light|led|bulb|lantern|torch|flashlight/', $text)) return 'light';
        if (preg_match('/solar/', $text)) return 'solar light';
        if (preg_match('/fairy|string.?light|decorat/', $text)) return 'decorative light';

        // Personal Care
        if (preg_match('/trimmer|shaver|razor|grooming/', $text)) return 'grooming tool';
        if (preg_match('/massag|body.?care|scrub|loofah/', $text)) return 'personal care';
        if (preg_match('/sunglass|eyewear|glass/', $text)) return 'sunglasses';
        if (preg_match('/hair.?dryer|straighten|curl/', $text)) return 'hair tool';
        if (preg_match('/makeup|cosmetic|beauty|mirror/', $text)) return 'beauty product';
        if (preg_match('/toothbrush|dental|oral/', $text)) return 'dental care';

        // Household Appliances
        if (preg_match('/fan|cooler|heater|humidifier|purifier|air/', $text)) return 'appliance';
        if (preg_match('/vacuum|cleaner/', $text)) return 'vacuum cleaner';
        if (preg_match('/sewing|machine/', $text)) return 'machine';

        // Bags
        if (preg_match('/backpack|rucksack/', $text)) return 'backpack';
        if (preg_match('/sling|crossbody|shoulder/', $text)) return 'sling bag';
        if (preg_match('/duffel|duffle|travel.?bag|luggage/', $text)) return 'travel bag';
        if (preg_match('/pouch|wallet|purse/', $text)) return 'pouch';
        if (preg_match('/bag/', $text)) return 'bag';

        // Toys & Kids
        if (preg_match('/toy|puzzle|game|building.?block|lego/', $text)) return 'toy';
        if (preg_match('/color|drawing|art|sketch|paint|craft/', $text)) return 'art set';
        if (preg_match('/book|magic.?book|story/', $text)) return 'book';

        // Stationery & Office
        if (preg_match('/pen|pencil|stationery|marker|highlighter|note/', $text)) return 'stationery';
        if (preg_match('/gift.?set|gift/', $text)) return 'gift set';

        // Vehicle
        if (preg_match('/car|vehicle|auto|bike|motor/', $text)) return 'car accessory';
        if (preg_match('/fastag/', $text)) return 'FASTag holder';

        // Fitness
        if (preg_match('/push.?up|abs|roller|skip|jump|yoga|fitness|exercise|gym/', $text)) return 'fitness gear';

        // Clothing accessories
        if (preg_match('/cap|beanie|muffler|scarf|glove|sock/', $text)) return 'winter wear';
        if (preg_match('/belt|buckle/', $text)) return 'belt';

        // Memory & Connectivity
        if (preg_match('/memory|sd.?card|otg|hub|adapter|hdmi|card.?reader/', $text)) return 'adapter';
        if (preg_match('/ring.?holder|pop.?socket|grip/', $text)) return 'phone grip';

        return 'product';
    }

    private function fillPlaceholders(string $template, Product $product, string $productType, string $timeframe = ''): string
    {
        $shortName = strlen($product->name) > 45 ? substr($product->name, 0, 42) . '...' : $product->name;

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
                'Exceeded my expectations',
                'Best purchase this month!',
                'Amazing quality {product_type}',
                'Worth every rupee',
                'Highly recommend this',
                'Fantastic {product_type}!',
                'So happy with this purchase',
                'Great value for money',
                'Very pleased with this {product_type}',
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
                'Better than expensive alternatives',
                'Using it daily, zero complaints',
                'Jikra never disappoints!',
                'My go-to store for such products',
            ],
            $rating >= 4 => [
                'Good quality {product_type}',
                'Happy with this purchase',
                'Nice {product_type}, minor things',
                'Pretty good for the price',
                'Solid {product_type}, would recommend',
                'Good value, decent quality',
                'Satisfied with this {product_type}',
                'Mostly impressed',
                'Nice design and build',
                'Good {product_type}, works well',
                'Decent quality, looks great',
                'Happy overall with this',
                'Good product, quick delivery',
                'A solid purchase overall',
                'Quite nice {product_type}',
                'Pleased with the quality',
                'Good for everyday use',
                'Better than expected',
                'Would buy again',
                'Good performance for the price',
                'Reliable {product_type}',
                'Does the job well',
                'Decent {product_type} from Jikra',
                'Recommended for the price',
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
                'Not bad, not great',
                'Meets basic expectations',
                'Could use improvements',
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
                'Expected better',
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
                'Bought this {product_type} and it turned out to be a great purchase. The build quality is excellent and it feels premium. Really happy with how it works.',
                'I have been looking for a good {product_type} for {timeframe} and finally found this one. Works perfectly and the quality is top notch for the price.',
                'This {product_type} is amazing! I have been using it for {timeframe} now and it still works like new. Definitely worth the money.',
                'Excellent product! The {product_type} works perfectly and feels very well built. Will definitely order more from Jikra.',
                'Very impressed with this {product_type}. It is exactly as shown in the pictures. Fast shipping too! No complaints at all.',
                'This is my third purchase from Jikra and every time the quality has been consistent. This {product_type} is no exception.',
                'I was skeptical about ordering online but this {product_type} changed my mind. The quality is outstanding for this price range.',
                'Just received this {product_type} and I am thoroughly impressed. Would definitely order from Jikra again.',
                'Fantastic quality for the price! The {product_type} is well-made and performs great. Very happy with this find.',
                'This {product_type} is honestly one of the best I have bought in this price range. Great build and works perfectly.',
                'Was looking for something reliable and this {product_type} did not disappoint. Great quality and great price.',
                'Absolutely love this {product_type}! I use it every day and it has not let me down once. The quality feels premium.',
                'I compared this with several other options and this {product_type} stood out for the quality. Been using it for {timeframe} and still works amazing.',
                'Really pleased with this purchase. The {product_type} is well-built and performs flawlessly. Good experience shopping at Jikra.',
                'Using this {product_type} for {timeframe} now. No issues whatsoever. Everything is top notch. Best value for money.',
                'Ordered this and was so impressed that I bought another one as a gift. The {product_type} performs brilliantly.',
                'The {product_type} arrived well packaged and works perfectly out of the box. Performance has been flawless for {timeframe} now.',
                'Hands down the best {product_type} I have used at this price point. Will recommend to everyone.',
                'This {product_type} from Jikra exceeded my expectations completely. The material and build quality are really good. Have been using it for {timeframe} and it is holding up great.',
                'Got this {product_type} on a friend\'s recommendation and I am not disappointed. Works exactly as described. Very happy with the purchase.',
            ],
            $rating >= 4 => [
                'Good {product_type} overall. It works well and the quality is decent for the price. Only minor thing is the build could be slightly sturdier.',
                'Bought this and it is a solid purchase. Nice build and good performance. Could be slightly better in terms of packaging.',
                'Happy with this {product_type}. Been using it for {timeframe}. The quality is good though I wish the finish was a bit more premium.',
                'Pretty good {product_type} for the price point. Works exactly as described. Delivery was prompt.',
                'Nice {product_type}! Works well for daily use. The quality is better than what I expected at this price range.',
                'Decent purchase. I use this {product_type} daily. It is well-made and performs reliably. Minor issue but overall satisfied.',
                'This {product_type} is quite nice. I use it regularly. Good quality and value for money. Shipping was fast.',
                'I like this {product_type} a lot. The build feels good and it works exactly like the pictures show.',
                'Solid {product_type} from Jikra. Been using it for {timeframe}. Good quality but delivery took a bit longer than expected.',
                'Good buy! The {product_type} works well and looks great. Would recommend with minor reservations.',
                'Overall a good {product_type}. Quality is good for the price. Just needs slightly better packaging.',
                'The {product_type} from {brand} is quite nice. Performs well for daily use. Just a couple of minor issues keeping it from 5 stars.',
                'Good product for the price. The {product_type} has been working great for {timeframe}. Build quality is solid.',
                'Satisfied with this {product_type}. It does what it promises. Minor improvements would make it perfect.',
                'Nice {product_type} from Jikra. Performance is good and the design looks nice. Value for money. Would buy again.',
            ],
            $rating >= 3 => [
                'The {product_type} is okay. Not bad but not exceptional either. It works but the build quality could be better for what I paid.',
                'Average {product_type}. It serves its purpose. The design is fine but the materials feel a bit cheap.',
                'Mixed feelings about this one. Looks nice but the quality does not feel premium. Fair for the price.',
                'It is alright. The {product_type} works but the performance is inconsistent sometimes. Okay product overall.',
                'Decent {product_type} for basic use. Had it for {timeframe} now. Acceptable for the price.',
                'Got this and it is average quality. The product photos look better than reality. Not bad but I expected more.',
                'The {product_type} is fine for everyday use. Quality is standard, nothing to write home about.',
                'OK product. I use it but it is not great. The build is acceptable and the performance is basic.',
                'Average purchase. The {product_type} looks decent but the quality is just okay. Fair value.',
                'Not great, not terrible. This {product_type} does the job. Some quality issues but still usable.',
            ],
            $rating >= 2 => [
                'Honestly expected better. The {product_type} quality is below what I thought I would get. The materials feel cheap.',
                'The {product_type} looked much better in the photos. In person, the quality is disappointing.',
                'Not happy with this purchase. The build quality is poor. Would not recommend at this price.',
                'Below average {product_type}. Started having issues within a few days. Quality control seems lacking.',
                'Disappointed with the quality of this {product_type}. For the price paid, I expected much better.',
                'Not worth it. The {product_type} quality is poor and does not match the product images at all.',
            ],
            default => [
                'Very poor quality {product_type}. The build feels terrible and it stopped working within days. Very disappointed.',
                'Terrible purchase. The {product_type} arrived with defects and barely works. Complete waste of money.',
                'Worst {product_type} I have bought. Cheap materials, poor build, and nothing like the pictures.',
            ],
        };
    }

    // ─── Pros/Cons Pools ─────────────────────────────────────

    private function getProsPool(string $productType): array
    {
        $general = [
            'Good build quality',
            'Fast delivery',
            'Nice packaging',
            'Value for money',
            'True to description',
            'Looks premium',
            'Easy to use',
            'Durable build',
            'Great for daily use',
        ];

        return match ($productType) {
            'earphones' => array_merge($general, [
                'Clear sound quality', 'Deep bass', 'Comfortable fit', 'Good noise cancellation',
                'Long battery life', 'Fast charging', 'No connectivity drops', 'Good mic for calls',
                'Lightweight and compact', 'Low latency gaming mode',
            ]),
            'speaker' => array_merge($general, [
                'Loud and clear sound', 'Punchy bass', 'Long battery life', 'Portable and lightweight',
                'Water resistant', 'Pairs quickly via Bluetooth', 'Good range', 'TWS pairing works well',
                'Great for outdoor use', 'Compact size',
            ]),
            'charger', 'cable' => array_merge($general, [
                'Charges fast', 'Sturdy build', 'No overheating', 'Compact design',
                'Works with all devices', 'Braided cable lasts long', 'USB-C compatible',
                'Good cable length', 'BIS certified', 'Multiple port support',
            ]),
            'power bank' => array_merge($general, [
                'Fast charging output', 'Charges phone fully 2-3 times', 'Slim and portable',
                'LED indicator helpful', 'USB-C input charges quickly', 'Great for travel',
                'Charges multiple devices', 'Solid build', 'Overcharge protection', 'Good capacity',
            ]),
            'phone case', 'screen protector' => array_merge($general, [
                'Perfect fit for phone model', 'Good drop protection', 'Buttons are clickable',
                'Camera cutout is precise', 'Does not add much bulk', 'No yellowing so far',
                'Anti-fingerprint', 'Easy to install', 'Raised edges protect screen', 'Looks great',
            ]),
            'smartwatch', 'watch strap' => array_merge($general, [
                'Bright display', 'Bluetooth calling works well', 'Accurate heart rate tracking',
                'Battery lasts 5-6 days', 'Comfortable to wear all day', 'Many watch face options',
                'SpO2 monitoring is useful', 'Quick Bluetooth pairing', 'Water resistant',
                'Sleep tracking is accurate',
            ]),
            'gaming accessory', 'cooling fan' => array_merge($general, [
                'Improved my gaming', 'Responsive trigger buttons', 'Phone stays cool',
                'Low latency', 'Comfortable grip', 'Easy to attach', 'Works great for BGMI',
                'Compact and portable', 'Noticeable difference in gameplay',
            ]),
            'car mount', 'selfie stick', 'stand', 'phone mount', 'phone grip' => array_merge($general, [
                'Strong grip', '360 degree rotation', 'Easy to install', 'Sturdy on desk',
                'Adjustable angle', 'Compact when folded', 'Works with all phone sizes',
                'Good suction', 'Perfect for video calls',
            ]),
            'kitchen appliance', 'kitchen tool', 'kitchenware' => array_merge($general, [
                'Powerful motor', 'Easy to clean', 'Compact size fits kitchen', 'Sharp blades',
                'Multiple speed settings', 'Saves cooking time', 'Good capacity',
                'Sturdy handle', 'Food-grade material', 'Easy to operate',
            ]),
            'bottle', 'container' => array_merge($general, [
                'Leak-proof seal', 'Keeps water cold', 'Easy to carry', 'BPA-free material',
                'Good capacity', 'Does not retain smell', 'Dishwasher safe',
                'Sturdy lid', 'Perfect size for travel',
            ]),
            'organizer', 'cleaning tool', 'bathroom accessory', 'laundry tool', 'household tool', 'dustbin', 'mat' => array_merge($general, [
                'Strong adhesive', 'Holds weight well', 'Easy to install', 'Does not damage walls',
                'Saves space', 'Multi-purpose use', 'Good material quality',
                'Waterproof', 'Easy to clean', 'Sturdy construction',
            ]),
            'light', 'solar light', 'decorative light' => array_merge($general, [
                'Bright LED light', 'Long battery life', 'Easy to install', 'Good brightness levels',
                'Auto on/off works well', 'Motion sensor is accurate', 'Weatherproof',
                'Looks beautiful at night', 'Energy efficient', 'Remote control is convenient',
            ]),
            'grooming tool', 'personal care', 'hair tool', 'beauty product', 'dental care' => array_merge($general, [
                'Comfortable to use', 'Good battery backup', 'Skin-friendly', 'Multiple attachments',
                'Easy to clean', 'Quiet operation', 'Rechargeable via USB',
                'Compact for travel', 'Good grip', 'Works as advertised',
            ]),
            'sunglasses' => array_merge($general, [
                'UV protection', 'Lightweight frame', 'Comfortable fit', 'Stylish design',
                'Scratch resistant lens', 'Good for driving', 'Comes with case',
            ]),
            'appliance', 'vacuum cleaner', 'machine' => array_merge($general, [
                'Powerful performance', 'Quiet operation', 'Easy to clean', 'Good suction',
                'Compact size', 'Multiple speed settings', 'Energy efficient',
                'Portable design', 'Long cord length', 'Sturdy build',
            ]),
            'backpack', 'sling bag', 'travel bag', 'bag', 'pouch' => array_merge($general, [
                'Spacious compartments', 'Sturdy zippers', 'Lightweight yet strong',
                'Water-resistant material', 'Comfortable straps', 'Attractive design',
                'Multiple pockets', 'Good stitching', 'Perfect size for daily use',
            ]),
            'toy', 'art set', 'book' => array_merge($general, [
                'Kids love it', 'Safe materials', 'Keeps kids engaged', 'Educational value',
                'Bright colours', 'Age-appropriate', 'Sturdy build', 'Non-toxic',
                'Great gift option', 'Promotes creativity',
            ]),
            'stationery', 'gift set' => array_merge($general, [
                'Good variety of items', 'Smooth writing', 'Attractive packaging',
                'Great for gifting', 'Good ink quality', 'Durable',
                'Kids loved it', 'Premium feel',
            ]),
            'car accessory', 'FASTag holder' => array_merge($general, [
                'Easy to install', 'Strong adhesive', 'Does not fall off',
                'Perfect fit', 'Good build for car use', 'Looks neat',
                'Weather resistant', 'Easy to clean',
            ]),
            'fitness gear', 'winter wear', 'belt' => array_merge($general, [
                'Comfortable to use', 'Durable material', 'Good grip',
                'Perfect for workouts', 'Lightweight', 'No slipping',
                'Good build quality', 'Fits well',
            ]),
            'adapter' => array_merge($general, [
                'Works with all devices', 'Fast data transfer', 'Compact design',
                'Plug and play', 'Reliable connection', 'Good build',
            ]),
            default => $general,
        };
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
            'Could be a bit sturdier',
            'Wish it came with a carry pouch',
            'Gets warm with extended use',
            'Slightly heavier than expected',
            'Would prefer better packaging',
            'Size is slightly different from expected',
        ];
    }
}
