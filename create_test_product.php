<?php

$cat = App\Models\Category::first();

$p = App\Models\Product::create([
    'name' => 'Test Product - Rs 1',
    'slug' => 'test-product-rs-1',
    'description' => 'This is a test product for checkout testing. Price Rs 1 only.',
    'short_description' => 'Test product for checkout flow testing.',
    'price' => 1,
    'mrp' => 10,
    'stock_quantity' => 100,
    'is_active' => true,
    'category_id' => $cat->id,
    'sku' => 'TEST-RS1-001',
]);

echo "Created! ID: {$p->id} | Slug: {$p->slug}\n";
echo "URL: https://jikra.in/product/{$p->slug}\n";
