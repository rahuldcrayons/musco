<?php

namespace App\Services;

use App\Models\Product;

class ReviewSchemaService
{
    public function getProductSchema(Product $product): array
    {
        $description = strip_tags($product->short_description ?? $product->description ?? '');
        if (empty($description)) {
            $description = $product->name . ' - Available at ' . config('app.name', 'Jikra');
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $description,
            'sku' => $product->sku ?? (string) $product->id,
            'url' => route('product.show', $product),
            'productID' => (string) $product->id,
        ];

        // Images (required by Google)
        $images = $product->images->pluck('url')->map(fn ($url) => url($url))->toArray();
        if (!empty($images)) {
            $schema['image'] = $images;
        } elseif ($product->primary_image_url) {
            $schema['image'] = [$product->primary_image_url];
        }

        // Brand
        if ($product->brand) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $product->brand->name,
            ];
        }

        // MPN if SKU available
        if ($product->sku) {
            $schema['mpn'] = $product->sku;
        }

        // Video (if product has video URL)
        if ($product->video_url) {
            $schema['video'] = [
                '@type' => 'VideoObject',
                'name' => $product->name . ' - Product Video',
                'description' => $description,
                'thumbnailUrl' => $images[0] ?? $product->primary_image_url,
                'contentUrl' => $product->video_url,
                'uploadDate' => $product->created_at->format('Y-m-d'),
            ];
        }

        // Offers
        $offer = [
            '@type' => 'Offer',
            'url' => route('product.show', $product),
            'priceCurrency' => 'INR',
            'price' => (float) number_format((float) $product->price, 2, '.', ''),
            'itemCondition' => 'https://schema.org/NewCondition',
            'availability' => $product->isInStock()
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'seller' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'Jikra'),
            ],
        ];

        if ($product->mrp > $product->price) {
            $offer['priceValidUntil'] = now()->addMonths(3)->format('Y-m-d');
        }

        $schema['offers'] = $offer;

        // Aggregate Rating + Reviews
        $approvedReviews = $product->approvedReviews;
        $reviewCount = $approvedReviews->count();

        if ($reviewCount > 0) {
            $avgRating = round($approvedReviews->avg('rating'), 1);

            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $avgRating,
                'ratingCount' => $reviewCount,
                'reviewCount' => $reviewCount,
                'bestRating' => 5,
                'worstRating' => 1,
            ];

            // Include up to 10 most recent reviews with content in structured data
            // Google requires reviewBody or name to be present for valid review markup
            $schema['review'] = $approvedReviews
                ->filter(fn ($r) => !empty($r->content) || !empty($r->title))
                ->sortByDesc('created_at')
                ->take(10)
                ->map(function ($review) {
                    $authorName = $review->user
                        ? $review->user->first_name . ' ' . strtoupper(substr($review->user->last_name ?? '', 0, 1)) . '.'
                        : ($review->guest_name ?? 'A Customer');

                    $reviewSchema = [
                        '@type' => 'Review',
                        'datePublished' => $review->created_at->format('Y-m-d'),
                        'reviewRating' => [
                            '@type' => 'Rating',
                            'ratingValue' => (int) $review->rating,
                            'bestRating' => 5,
                            'worstRating' => 1,
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name' => $authorName,
                        ],
                    ];

                    if (!empty($review->title)) {
                        $reviewSchema['name'] = $review->title;
                    }

                    if (!empty($review->content)) {
                        $reviewSchema['reviewBody'] = $review->content;
                    }

                    return $reviewSchema;
                })->values()->toArray();
        }

        return $schema;
    }

    public function getFaqSchema(Product $product): ?array
    {
        $questions = $product->questions->filter(fn ($q) => $q->is_answered && $q->answers->isNotEmpty());

        if ($questions->isEmpty()) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $questions->map(function ($question) {
                return [
                    '@type' => 'Question',
                    'name' => $question->question,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $question->answers->first()->answer,
                    ],
                ];
            })->values()->toArray(),
        ];
    }
}
