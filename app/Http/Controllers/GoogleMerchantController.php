<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class GoogleMerchantController extends Controller
{
    public function __invoke(): Response
    {
        $content = Cache::remember('google-merchant-feed', 3600, function () {
            $products = Product::query()
                ->where('is_active', true)
                ->with(['category', 'brand', 'primaryImage', 'images', 'approvedReviews'])
                ->orderBy('id')
                ->get();

            $appUrl = rtrim(config('app.url'), '/');
            $appName = config('app.name', 'Jikra');

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">' . "\n";
            $xml .= "<channel>\n";
            $xml .= "  <title>{$this->esc($appName)} - Google Merchant Feed</title>\n";
            $xml .= "  <link>{$appUrl}</link>\n";
            $xml .= "  <description>Google Merchant Center product feed for {$this->esc($appName)}</description>\n";

            foreach ($products as $product) {
                $xml .= $this->buildItem($product, $appUrl);
            }

            $xml .= "</channel>\n";
            $xml .= '</rss>';

            return $xml;
        });

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function buildItem(Product $product, string $appUrl): string
    {
        $availability = $product->isInStock() ? 'in_stock' : 'out_of_stock';
        $link = $appUrl . '/product/' . $product->slug;
        $imageUrl = $this->absUrl($product->primary_image_url, $appUrl);

        $description = strip_tags($product->short_description ?: mb_substr($product->description ?? '', 0, 5000));
        if (empty($description)) {
            $description = $product->name;
        }

        $item = "  <item>\n";
        $item .= "    <g:id>{$product->id}</g:id>\n";
        $item .= "    <g:title>{$this->esc($product->name)}</g:title>\n";
        $item .= "    <g:description>{$this->esc($description)}</g:description>\n";
        $item .= "    <g:link>{$link}</g:link>\n";
        $item .= "    <g:image_link>{$this->esc($imageUrl)}</g:image_link>\n";

        // Additional images (up to 10)
        $additionalImages = $product->images->take(10);
        foreach ($additionalImages as $img) {
            $imgUrl = $this->absUrl($img->url, $appUrl);
            if ($imgUrl !== $imageUrl) {
                $item .= "    <g:additional_image_link>{$this->esc($imgUrl)}</g:additional_image_link>\n";
            }
        }

        $item .= "    <g:availability>{$availability}</g:availability>\n";
        $item .= "    <g:condition>new</g:condition>\n";

        // Pricing
        if ($product->mrp && $product->price < $product->mrp) {
            $item .= "    <g:price>" . $this->fmtPrice($product->mrp) . "</g:price>\n";
            $item .= "    <g:sale_price>" . $this->fmtPrice($product->price) . "</g:sale_price>\n";
        } else {
            $item .= "    <g:price>" . $this->fmtPrice($product->price) . "</g:price>\n";
        }

        // Brand (required by Google)
        $brandName = $product->brand?->name ?: config('app.name', 'Jikra');
        $item .= "    <g:brand>{$this->esc($brandName)}</g:brand>\n";

        // Identifiers
        if ($product->sku) {
            $item .= "    <g:mpn>{$this->esc($product->sku)}</g:mpn>\n";
        }

        if ($product->barcode) {
            $item .= "    <g:gtin>{$this->esc($product->barcode)}</g:gtin>\n";
        }

        // If no GTIN or MPN
        if (!$product->barcode && !$product->sku) {
            $item .= "    <g:identifier_exists>false</g:identifier_exists>\n";
        }

        // Category
        if ($product->category) {
            $item .= "    <g:product_type>{$this->esc($product->category->name)}</g:product_type>\n";
        }

        // Shipping — free for orders above 499
        $item .= "    <g:shipping>\n";
        $item .= "      <g:country>IN</g:country>\n";
        $item .= "      <g:service>Standard</g:service>\n";
        $item .= "      <g:price>" . ($product->price >= 499 ? '0.00 INR' : '49.00 INR') . "</g:price>\n";
        $item .= "    </g:shipping>\n";

        // Return policy
        $item .= "    <g:return_policy_label>7-day-return</g:return_policy_label>\n";

        return $item . "  </item>\n";
    }

    private function fmtPrice(float $price): string
    {
        return number_format($price, 2, '.', '') . ' INR';
    }

    private function absUrl(?string $url, string $appUrl): string
    {
        if (!$url) {
            return '';
        }
        if (str_starts_with($url, 'http')) {
            return $url;
        }
        return $appUrl . '/' . ltrim($url, '/');
    }

    private function esc(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
