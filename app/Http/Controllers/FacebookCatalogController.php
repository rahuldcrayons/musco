<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class FacebookCatalogController extends Controller
{
    /**
     * Generate an XML product feed for Facebook Commerce Manager.
     * URL: /feeds/facebook-catalog.xml
     */
    public function __invoke(): Response
    {
        $products = Product::query()
            ->where('is_active', true)
            ->with(['category', 'brand', 'primaryImage', 'images'])
            ->orderBy('id')
            ->get();

        $appUrl = rtrim(config('app.url'), '/');
        $appName = config('app.name', 'Jikra');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">' . "\n";
        $xml .= "<channel>\n";
        $xml .= "  <title>{$this->escape($appName)} Product Catalog</title>\n";
        $xml .= "  <link>{$appUrl}</link>\n";
        $xml .= "  <description>Product catalog for {$this->escape($appName)}</description>\n";

        foreach ($products as $product) {
            $xml .= $this->buildItem($product, $appUrl);
        }

        $xml .= "</channel>\n";
        $xml .= '</rss>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function buildItem(Product $product, string $appUrl): string
    {
        $availability = $product->isInStock() ? 'in stock' : 'out of stock';
        $price = number_format((float) $product->price, 2, '.', '') . ' INR';
        $link = $appUrl . '/product/' . $product->slug;
        $imageUrl = $product->primary_image_url;

        // Ensure absolute image URL
        if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
            $imageUrl = $appUrl . '/' . ltrim($imageUrl, '/');
        }

        $description = $product->short_description
            ?: strip_tags(mb_substr($product->description ?? '', 0, 5000));

        $item = "  <item>\n";
        $item .= "    <g:id>{$product->id}</g:id>\n";
        $item .= "    <g:title>{$this->escape($product->name)}</g:title>\n";
        $item .= "    <g:description>{$this->escape($description)}</g:description>\n";
        $item .= "    <g:link>{$link}</g:link>\n";
        $item .= "    <g:image_link>{$this->escape($imageUrl)}</g:image_link>\n";

        // Additional images
        $additionalImages = $product->images->where('is_primary', false)->take(10);
        foreach ($additionalImages as $img) {
            $imgUrl = $img->url;
            if ($imgUrl && !str_starts_with($imgUrl, 'http')) {
                $imgUrl = $appUrl . '/' . ltrim($imgUrl, '/');
            }
            $item .= "    <g:additional_image_link>{$this->escape($imgUrl)}</g:additional_image_link>\n";
        }

        $item .= "    <g:availability>{$availability}</g:availability>\n";
        $item .= "    <g:condition>new</g:condition>\n";

        // Price: use MRP as base price if discounted, otherwise use price
        if ($product->mrp && $product->price < $product->mrp) {
            $mrpPrice = number_format((float) $product->mrp, 2, '.', '') . ' INR';
            $salePrice = number_format((float) $product->price, 2, '.', '') . ' INR';
            $item .= "    <g:price>{$mrpPrice}</g:price>\n";
            $item .= "    <g:sale_price>{$salePrice}</g:sale_price>\n";
        } else {
            $item .= "    <g:price>{$price}</g:price>\n";
        }

        if ($product->brand) {
            $item .= "    <g:brand>{$this->escape($product->brand->name)}</g:brand>\n";
        }

        if ($product->sku) {
            $item .= "    <g:mpn>{$this->escape($product->sku)}</g:mpn>\n";
        }

        if ($product->barcode) {
            $item .= "    <g:gtin>{$this->escape($product->barcode)}</g:gtin>\n";
        }

        if ($product->category) {
            $item .= "    <g:product_type>{$this->escape($product->category->name)}</g:product_type>\n";
        }

        $item .= "  </item>\n";

        return $item;
    }

    private function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
