<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            JewelleryCategorySeeder::class,
            MusCoBrandSeeder::class,
            SellerSeeder::class,
            BannerSeeder::class,
            CouponSeeder::class,
            SettingSeeder::class,
            LegalPageSeeder::class,
            ReviewSettingsSeeder::class,
            ChatbotSettingsSeeder::class,
            BlogPostSeeder::class,
            JewelleryAttributeSeeder::class,
        ]);
    }
}
