<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MusCoBrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            // Premium Indian Jewellery Brands
            ['name' => 'Tanishq', 'description' => 'A Tata Product — India\'s Most Trusted Jewellery Brand', 'is_featured' => true],
            ['name' => 'Malabar Gold & Diamonds', 'description' => 'Celebrating Every Moment with Gold', 'is_featured' => true],
            ['name' => 'CaratLane', 'description' => 'Jewellery That Tells Your Story', 'is_featured' => true],
            ['name' => 'Kalyan Jewellers', 'description' => 'Trust, Tradition & Timeless Beauty', 'is_featured' => true],
            ['name' => 'PC Jeweller', 'description' => 'Purity You Can Trust', 'is_featured' => true],
            ['name' => 'Senco Gold', 'description' => 'Where Heritage Meets Modern Design', 'is_featured' => true],

            // Popular Jewellery Brands
            ['name' => 'Joyalukkas', 'description' => 'The World\'s Favourite Jeweller', 'is_featured' => true],
            ['name' => 'TBZ - The Original', 'description' => 'Celebrating Life\'s Beautiful Moments', 'is_featured' => true],
            ['name' => 'GRT Jewellers', 'description' => 'Trust of Generations', 'is_featured' => false],
            ['name' => 'Titan Raga', 'description' => 'For the Woman of Today', 'is_featured' => false],
            ['name' => 'BlueStone', 'description' => 'Fine Jewellery for Every Day', 'is_featured' => false],
            ['name' => 'Swarovski', 'description' => 'Crystal Brilliance Since 1895', 'is_featured' => false],
            ['name' => 'Pandora', 'description' => 'Unforgettable Moments', 'is_featured' => false],
            ['name' => 'Mia by Tanishq', 'description' => 'Celebrating the Modern Indian Woman', 'is_featured' => false],

            // Silver & Contemporary
            ['name' => 'Giva', 'description' => 'Silver Jewellery, Crafted with Love', 'is_featured' => false],
            ['name' => 'Zaveri Pearls', 'description' => 'Elegance in Every Pearl', 'is_featured' => false],
            ['name' => 'Candere', 'description' => 'Lightweight Jewellery for Every Day', 'is_featured' => false],
            ['name' => 'Melorra', 'description' => 'Trendy Gold Jewellery', 'is_featured' => false],
            ['name' => 'Tiffany & Co.', 'description' => 'The World\'s Premier Jeweller', 'is_featured' => false],
            ['name' => 'Cartier', 'description' => 'Luxury Jewellery Since 1847', 'is_featured' => false],
        ];

        $position = 1;
        foreach ($brands as $brandData) {
            Brand::create([
                'name' => $brandData['name'],
                'slug' => Str::slug($brandData['name']),
                'description' => $brandData['description'],
                'is_active' => true,
                'is_featured' => $brandData['is_featured'],
                'position' => $position++,
            ]);
        }
    }
}
