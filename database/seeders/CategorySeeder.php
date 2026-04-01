<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // This seeder is replaced by JewelleryCategorySeeder
        // Kept for backwards compatibility but does nothing
        $this->command->info('CategorySeeder skipped — use JewelleryCategorySeeder instead.');
    }
}
