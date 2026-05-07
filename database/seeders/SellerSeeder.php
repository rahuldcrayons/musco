<?php

namespace Database\Seeders;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = [
            [
                'store_name' => 'Trendimus Store',
                'business_name' => 'Trendimus',
                'email' => 'seller1@example.com',
                'description' => 'Your trusted destination for certified gold, diamond & silver jewellery.',
            ],
            [
                'store_name' => 'Gold Heritage',
                'business_name' => 'Gold Heritage Jewellers',
                'email' => 'seller2@example.com',
                'description' => 'Traditional and contemporary gold jewellery with BIS hallmark certification.',
            ],
            [
                'store_name' => 'Diamond Dreams',
                'business_name' => 'Diamond Dreams Pvt. Ltd.',
                'email' => 'seller3@example.com',
                'description' => 'Premium certified diamond jewellery for every occasion.',
            ],
            [
                'store_name' => 'Silver Craft',
                'business_name' => 'Silver Craft Trading',
                'email' => 'seller4@example.com',
                'description' => 'Handcrafted silver jewellery and accessories with 925 purity.',
            ],
        ];

        foreach ($sellers as $sellerData) {
            // Create user for seller
            $user = User::create([
                'first_name' => explode(' ', $sellerData['store_name'])[0],
                'last_name' => 'Seller',
                'email' => $sellerData['email'],
                'password' => Hash::make('password'),
                'role' => 'seller',
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            Seller::create([
                'user_id' => $user->id,
                'store_name' => $sellerData['store_name'],
                'business_name' => $sellerData['business_name'],
                'slug' => \Illuminate\Support\Str::slug($sellerData['store_name']),
                'store_description' => $sellerData['description'],
                'description' => $sellerData['description'],
                'status' => 'approved',
                'commission_rate' => 15,
                'available_balance' => rand(100, 5000),
                'pending_balance' => rand(50, 1000),
                'email_notifications' => true,
                'order_notifications' => true,
                'review_notifications' => true,
                'approved_at' => now(),
            ]);
        }
    }
}
