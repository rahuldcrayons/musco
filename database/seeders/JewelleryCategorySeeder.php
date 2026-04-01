<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JewelleryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Rings',
                'description' => 'Exquisite rings for every occasion — engagement, wedding, fashion and more',
                'icon' => 'sparkles',
                'is_featured' => true,
                'children' => [
                    ['name' => 'Engagement Rings', 'description' => 'Diamond and gemstone engagement rings'],
                    ['name' => 'Wedding Bands', 'description' => 'Classic and contemporary wedding bands'],
                    ['name' => 'Fashion Rings', 'description' => 'Statement and everyday fashion rings'],
                    ['name' => 'Eternity Rings', 'description' => 'Full and half eternity bands'],
                    ['name' => 'Cocktail Rings', 'description' => 'Bold and glamorous cocktail rings'],
                    ['name' => 'Stackable Rings', 'description' => 'Mix and match stackable ring sets'],
                ],
            ],
            [
                'name' => 'Necklaces & Pendants',
                'description' => 'Elegant necklaces, chains and pendant sets',
                'icon' => 'gift',
                'is_featured' => true,
                'children' => [
                    ['name' => 'Chains', 'description' => 'Gold, silver and platinum chains'],
                    ['name' => 'Pendants', 'description' => 'Diamond and gemstone pendants'],
                    ['name' => 'Pendant Sets', 'description' => 'Matching pendant and earring sets'],
                    ['name' => 'Mangalsutra', 'description' => 'Traditional and modern mangalsutra designs'],
                    ['name' => 'Chokers', 'description' => 'Trendy choker necklaces'],
                    ['name' => 'Layered Necklaces', 'description' => 'Multi-layered fashion necklaces'],
                ],
            ],
            [
                'name' => 'Earrings',
                'description' => 'From studs to chandeliers — earrings for every style',
                'icon' => 'star',
                'is_featured' => true,
                'children' => [
                    ['name' => 'Studs', 'description' => 'Classic diamond and gemstone studs'],
                    ['name' => 'Hoops', 'description' => 'Gold and diamond hoop earrings'],
                    ['name' => 'Drops & Danglers', 'description' => 'Elegant drop and dangler earrings'],
                    ['name' => 'Jhumkas', 'description' => 'Traditional Indian jhumka earrings'],
                    ['name' => 'Chandbali', 'description' => 'Exquisite chandbali earrings'],
                    ['name' => 'Ear Cuffs', 'description' => 'Modern ear cuff designs'],
                ],
            ],
            [
                'name' => 'Bangles & Bracelets',
                'description' => 'Stunning bangles and bracelets in gold, diamond and more',
                'icon' => 'heart',
                'is_featured' => true,
                'children' => [
                    ['name' => 'Gold Bangles', 'description' => 'Traditional and modern gold bangles'],
                    ['name' => 'Diamond Bangles', 'description' => 'Diamond-studded luxury bangles'],
                    ['name' => 'Bracelets', 'description' => 'Chain and charm bracelets'],
                    ['name' => 'Tennis Bracelets', 'description' => 'Classic diamond tennis bracelets'],
                    ['name' => 'Kada', 'description' => 'Traditional Indian kada designs'],
                    ['name' => 'Charm Bracelets', 'description' => 'Customizable charm bracelets'],
                ],
            ],
            [
                'name' => 'Nose Pins & Rings',
                'description' => 'Delicate nose pins and nose rings in gold and diamond',
                'icon' => 'sparkles',
                'is_featured' => false,
                'children' => [
                    ['name' => 'Nose Pins', 'description' => 'Diamond and gold nose pins'],
                    ['name' => 'Nose Rings', 'description' => 'Traditional nose ring designs'],
                    ['name' => 'Septum Rings', 'description' => 'Trendy septum ring styles'],
                ],
            ],
            [
                'name' => 'Men\'s Jewellery',
                'description' => 'Sophisticated jewellery designed for men',
                'icon' => 'user',
                'is_featured' => true,
                'children' => [
                    ['name' => 'Men\'s Rings', 'description' => 'Bold rings and bands for men'],
                    ['name' => 'Men\'s Chains', 'description' => 'Gold and silver chains for men'],
                    ['name' => 'Men\'s Bracelets', 'description' => 'Leather and metal bracelets'],
                    ['name' => 'Cufflinks', 'description' => 'Premium cufflinks for formal wear'],
                    ['name' => 'Tie Pins', 'description' => 'Gold and silver tie pins'],
                ],
            ],
            [
                'name' => 'Gold Coins & Bars',
                'description' => 'Investment gold coins and bars with BIS hallmark',
                'icon' => 'currency-rupee',
                'is_featured' => false,
                'children' => [
                    ['name' => 'Gold Coins', 'description' => 'BIS hallmarked gold coins'],
                    ['name' => 'Silver Coins', 'description' => 'Pure silver coins and bars'],
                    ['name' => 'Gold Bars', 'description' => 'Investment-grade gold bars'],
                ],
            ],
            [
                'name' => 'Wedding Jewellery',
                'description' => 'Complete bridal and wedding jewellery collections',
                'icon' => 'heart',
                'is_featured' => true,
                'children' => [
                    ['name' => 'Bridal Sets', 'description' => 'Complete bridal jewellery sets'],
                    ['name' => 'Maang Tikka', 'description' => 'Bridal maang tikka designs'],
                    ['name' => 'Haath Phool', 'description' => 'Traditional hand chain jewellery'],
                    ['name' => 'Waist Chains', 'description' => 'Bridal kamarband and waist chains'],
                    ['name' => 'Anklets', 'description' => 'Gold and silver anklets for brides'],
                ],
            ],
            [
                'name' => 'Kids Jewellery',
                'description' => 'Safe and adorable jewellery for children',
                'icon' => 'star',
                'is_featured' => false,
                'children' => [
                    ['name' => 'Kids Earrings', 'description' => 'Lightweight earrings for kids'],
                    ['name' => 'Kids Bangles', 'description' => 'Small bangles and bracelets for children'],
                    ['name' => 'Kids Pendants', 'description' => 'Fun pendant designs for kids'],
                    ['name' => 'Kids Rings', 'description' => 'Adjustable rings for children'],
                ],
            ],
            [
                'name' => 'Silver Jewellery',
                'description' => 'Sterling silver jewellery at affordable prices',
                'icon' => 'sparkles',
                'is_featured' => false,
                'children' => [
                    ['name' => 'Silver Rings', 'description' => '925 sterling silver rings'],
                    ['name' => 'Silver Earrings', 'description' => 'Silver studs, hoops and danglers'],
                    ['name' => 'Silver Necklaces', 'description' => 'Silver chains and pendants'],
                    ['name' => 'Silver Bracelets', 'description' => 'Sterling silver bracelets'],
                    ['name' => 'Silver Anklets', 'description' => 'Traditional and modern silver anklets'],
                ],
            ],
        ];

        $position = Category::max('position') ?? 0;

        foreach ($categories as $categoryData) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($categoryData['name'])],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'icon' => $categoryData['icon'] ?? null,
                    'position' => ++$position,
                    'is_active' => true,
                    'is_featured' => $categoryData['is_featured'] ?? false,
                ]
            );

            if (!empty($categoryData['children'])) {
                $childPosition = 1;
                foreach ($categoryData['children'] as $childData) {
                    Category::firstOrCreate(
                        ['slug' => Str::slug($childData['name'])],
                        [
                            'parent_id' => $parent->id,
                            'name' => $childData['name'],
                            'description' => $childData['description'],
                            'position' => $childPosition++,
                            'is_active' => true,
                            'is_featured' => false,
                        ]
                    );
                }
            }
        }
    }
}
