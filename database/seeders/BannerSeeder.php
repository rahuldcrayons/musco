<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'name' => 'New Jewellery Collection - Up to 50% Off',
                'position' => 'hero',
                'image_url' => 'banners/hero-1.jpg',
                'mobile_image_url' => 'banners/hero-1-mobile.jpg',
                'link' => '/collections/new-arrivals',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Jewellery Festival - Exclusive Designs',
                'position' => 'hero',
                'image_url' => 'banners/hero-2.jpg',
                'mobile_image_url' => 'banners/hero-2-mobile.jpg',
                'link' => '/deals',
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Free Shipping on Orders Above ₹999',
                'position' => 'hero',
                'image_url' => 'banners/hero-3.jpg',
                'mobile_image_url' => 'banners/hero-3-mobile.jpg',
                'link' => '/products',
                'priority' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Wedding Jewellery - Bridal Collections',
                'position' => 'sidebar',
                'image_url' => 'banners/promo-1.jpg',
                'mobile_image_url' => null,
                'link' => '/category/wedding-jewellery',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Gold Coins & Bars - Invest in Gold',
                'position' => 'sidebar',
                'image_url' => 'banners/promo-2.jpg',
                'mobile_image_url' => null,
                'link' => '/category/gold-coins-bars',
                'priority' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $bannerData) {
            Banner::create($bannerData);
        }
    }
}
