<?php

// Backdate all generated reviews from today across past 180 days
// Run: php artisan tinker < backdate_reviews.php

use App\Models\Review;
use Illuminate\Support\Facades\DB;

$count = DB::table('reviews')
    ->where('is_generated', true)
    ->whereDate('created_at', today())
    ->count();

echo "Reviews to backdate: {$count}\n";

DB::table('reviews')
    ->where('is_generated', true)
    ->whereDate('created_at', today())
    ->chunkById(200, function ($reviews) {
        foreach ($reviews as $review) {
            $days = rand(1, 180);
            $hour = rand(6, 22);
            $minute = rand(0, 59);
            $second = rand(0, 59);
            $date = now()->subDays($days)->setTime($hour, $minute, $second);

            DB::table('reviews')
                ->where('id', $review->id)
                ->update(['created_at' => $date, 'updated_at' => $date]);
        }
    });

echo "Done backdating!\n";
