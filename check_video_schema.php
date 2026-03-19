<?php

// Check video JSON-LD schema
// Run: php artisan tinker < check_video_schema.php

$p = App\Models\Product::whereNotNull('video_url')
    ->where('video_url', '!=', '')
    ->with(['images', 'brand', 'approvedReviews.user', 'questions.answers'])
    ->first();

if (!$p) {
    echo "No products with video_url found\n";

    // Check if column exists
    $cols = Schema::getColumnListing('products');
    echo "Has video_url column: " . (in_array('video_url', $cols) ? 'YES' : 'NO') . "\n";
    echo "Total products: " . App\Models\Product::count() . "\n";
    exit;
}

echo "Product: {$p->name}\n";
echo "Video URL: {$p->video_url}\n\n";

$schema = app(App\Services\ReviewSchemaService::class)->getProductSchema($p);

echo json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
