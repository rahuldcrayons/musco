<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportMuscoProducts extends Command
{
    protected $signature   = 'trendymus:import-products-old';
    protected $description = '[DEPRECATED] Use trendymus:import-excel instead';

    // USD → INR rate (fetched once)
    private float $usdRate = 92.81;

    // Excel category name → DB category ID map
    private array $categoryMap = [];

    public function handle(): int
    {
        @ini_set('memory_limit', '512M');

        $this->info('Fetching USD→INR rate...');
        $rate = $this->getLiveRate();
        $this->usdRate = $rate;
        $this->info("Rate: 1 USD = ₹{$rate}");

        $this->info('Resolving categories...');
        $this->buildCategoryMap();

        $path = 'D:/projects/trendymus/MU & co.xlsx';
        $this->info("Loading Excel: {$path}");
        $spreadsheet = IOFactory::load($path);

        $totalCreated = 0;
        $totalSkipped = 0;

        foreach ($spreadsheet->getSheetNames() as $idx => $sheetName) {
            $sheet = $spreadsheet->getSheet($idx);
            $rows  = $sheet->toArray(null, true, true, true);

            $this->info("─── Sheet: {$sheetName} ───");

            if ($sheetName === 'Sheet1') {
                [$c, $s] = $this->importSheet1($rows);
            } else {
                [$c, $s] = $this->importOtherSheet($rows, $sheetName);
            }

            $totalCreated += $c;
            $totalSkipped += $s;
            $this->line("  Created: {$c}  Skipped: {$s}");
        }

        $this->newLine();
        $this->info("✓ Done — Total created: {$totalCreated}, skipped: {$totalSkipped}");
        return 0;
    }

    // ─── Sheet1 ───────────────────────────────────────────────────────────────
    // Columns: S.No | Product Name | Category | Sizes | Compare price | Color |
    //          Selling price | Stock | Status | Description | Image
    private function importSheet1(array $rows): array
    {
        $created = 0;
        $skipped = 0;

        foreach ($rows as $rowNum => $row) {
            if ($rowNum <= 2) continue; // skip blank + header

            $name        = $this->val($row['B']);
            $categoryRaw = $this->val($row['C']);
            $sizes       = $this->val($row['D']);
            $compareUsd  = $this->price($row['E']);
            $color       = $this->val($row['F']);
            $sellUsd     = $this->price($row['G']);
            $stock       = (int) $this->val($row['H']);
            $statusRaw   = $this->val($row['I']);
            $description = $this->val($row['J']);

            // Skip empty rows
            if (empty($name) && empty($categoryRaw)) { $skipped++; continue; }
            if (empty($name)) { $skipped++; continue; }

            $categoryId = $this->resolveCategory($categoryRaw);
            $priceInr   = $sellUsd > 0 ? $this->toInr($sellUsd) : null;
            $mrpInr     = $compareUsd > 0 ? $this->toInr($compareUsd) : ($priceInr ? $priceInr * 1.5 : null);

            $slug = $this->uniqueSlug($name);

            Product::create([
                'uuid'              => Str::uuid(),
                'seller_id'         => 1,
                'category_id'       => $categoryId,
                'name'              => $name,
                'slug'              => $slug,
                'description'       => $this->cleanText($description),
                'short_description' => $this->makeShortDesc($description, $name),
                'sku'               => strtoupper(Str::random(6)) . '-S1',
                'price'             => $priceInr ?? 999,
                'mrp'               => $mrpInr ?? 1499,
                'cost_price'        => null,
                'stock_quantity'    => $stock ?: 2,
                'stock_status'      => 'in_stock',
                'metal_color'       => $color ?: null,
                'is_active'         => true,
                'status'            => 'approved',
                'published_at'      => now(),
                'attributes'        => $this->buildAttributes($color, $sizes),
                'specifications'    => $this->buildSpecs($color, $sizes),
            ]);
            $created++;
        }

        return [$created, $skipped];
    }

    // ─── Other sheets ─────────────────────────────────────────────────────────
    // Columns: S no | Item No | category | item code | Product Name | Description |
    //          images | listing price | compare price | O.P | Stock | Color | Status
    //          (Bangles also has: size)
    private function importOtherSheet(array $rows, string $sheetName): array
    {
        $created = 0;
        $skipped = 0;

        // Detect header row index
        $headerRow = null;
        foreach ($rows as $rowNum => $row) {
            $firstCell = strtolower(trim((string)($row['A'] ?? '')));
            if (in_array($firstCell, ['s no', 's. no.', 's.no', '1'])) {
                $headerRow = $rowNum;
                break;
            }
        }
        if (!$headerRow) { return [0, 0]; }

        foreach ($rows as $rowNum => $row) {
            if ($rowNum <= $headerRow) continue;

            $itemCode    = $this->val($row['D']);
            $categoryRaw = $this->val($row['C']);
            $itemNo      = $this->val($row['B']); // NH... supplier code
            $name        = $this->val($row['E']);
            $description = $this->val($row['F']);
            $sellUsd     = $this->price($row['H']);
            $compareUsd  = $this->price($row['I']);
            $costUsd     = $this->price($row['J']);
            $stock       = (int) $this->val($row['K']);
            $color       = $this->val($row['L']);
            $statusRaw   = $this->val($row['M']);
            $size        = isset($row['N']) ? $this->val($row['N']) : null;

            // Skip entirely empty rows
            if (empty($itemCode) && empty($categoryRaw)) { $skipped++; continue; }
            if (empty($itemCode)) { $skipped++; continue; }

            // Generate name if missing
            if (empty($name)) {
                $name = $this->generateName($categoryRaw, $itemCode, $sheetName);
            }

            $categoryId = $this->resolveCategory($categoryRaw ?: $this->sheetToCategory($sheetName));
            $priceInr   = $sellUsd > 0  ? $this->toInr($sellUsd)   : $this->defaultPrice($categoryRaw);
            $mrpInr     = $compareUsd > 0 ? $this->toInr($compareUsd) : round($priceInr * 1.6);
            $costInr    = $costUsd > 0  ? $this->toInr($costUsd)   : null;

            $slug = $this->uniqueSlug($name);

            Product::create([
                'uuid'              => Str::uuid(),
                'seller_id'         => 1,
                'category_id'       => $categoryId,
                'name'              => $name,
                'slug'              => $slug,
                'description'       => $this->cleanText($description),
                'short_description' => $this->makeShortDesc($description, $name),
                'sku'               => $itemCode,
                'barcode'           => null,
                'price'             => $priceInr,
                'mrp'               => $mrpInr,
                'cost_price'        => $costInr,
                'stock_quantity'    => $stock ?: 2,
                'stock_status'      => 'in_stock',
                'metal_color'       => $color ?: null,
                'is_active'         => true,
                'status'            => 'approved',
                'published_at'      => now(),
                'attributes'        => $this->buildAttributes($color, $size),
                'specifications'    => $this->buildSpecs($color, $size, $itemNo),
            ]);
            $created++;
        }

        return [$created, $skipped];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function getLiveRate(): float
    {
        try {
            $json = @file_get_contents('https://open.er-api.com/v6/latest/USD', false,
                stream_context_create(['http' => ['timeout' => 5]])
            );
            if ($json) {
                $data = json_decode($json, true);
                if (isset($data['rates']['INR'])) {
                    return round((float) $data['rates']['INR'], 4);
                }
            }
        } catch (\Throwable) {}
        return 92.81; // fallback
    }

    private function buildCategoryMap(): void
    {
        // Create Jewellery parent
        $jewellery = Category::firstOrCreate(
            ['slug' => 'jewellery'],
            ['name' => 'Jewellery', 'description' => 'Fine and fashion jewellery', 'is_active' => true]
        );

        $make = fn(string $name, string $slug, int $parentId, string $desc = '') =>
            Category::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'parent_id' => $parentId, 'description' => $desc, 'is_active' => true]
            )->id;

        $jId = $jewellery->id;

        $rings         = $make('Rings',                'jewellery-rings',          $jId, 'Fashion and engagement rings');
        $necklaces     = $make('Necklaces & Pendants', 'jewellery-necklaces',      $jId, 'Necklaces, pendants and chains');
        $pendantSets   = $make('Pendant Sets',         'jewellery-pendant-sets',   $necklaces, 'Matching pendant jewellery sets');
        $earrings      = $make('Earrings',             'jewellery-earrings',       $jId, 'Studs, hoops and drop earrings');
        $banglesBraces = $make('Bangles & Bracelets',  'jewellery-bangles',        $jId, 'Bangles, cuffs and bracelets');
        $bangles       = $make('Bangles',              'jewellery-bangles-only',   $banglesBraces, 'Gold and silver bangles');
        $bracelets     = $make('Bracelets',            'jewellery-bracelets',      $banglesBraces, 'Chain and charm bracelets');
        $nosePins      = $make('Nose Pins',            'jewellery-nose-pins',      $jId, 'Gold and diamond nose pins');
        $anklets       = $make('Anklets',              'jewellery-anklets',        $jId, 'Gold and silver anklets');
        $accessories   = $make('Accessories',          'jewellery-accessories',    $jId, 'Jewellery accessories and gift sets');

        $this->categoryMap = [
            'pendant set'   => $pendantSets,
            'pendent set'   => $pendantSets,
            'pendant sets'  => $pendantSets,
            'necklace'      => $necklaces,
            'necklaces'     => $necklaces,
            'earrings'      => $earrings,
            'earings'       => $earrings,
            'rings'         => $rings,
            'ring'          => $rings,
            'bracelet'      => $bracelets,
            'bracelets'     => $bracelets,
            'bangles'       => $bangles,
            'bangle'        => $bangles,
            'nose pin'      => $nosePins,
            'nose pins'     => $nosePins,
            'anklet'        => $anklets,
            'anklets'       => $anklets,
            'accessories'   => $accessories,
        ];

        $this->info('  Categories ready: ' . implode(', ', array_unique($this->categoryMap)));
    }

    private function resolveCategory(string $raw): int
    {
        $key = strtolower(trim($raw));
        return $this->categoryMap[$key]
            ?? $this->categoryMap[str_replace(['s '], [''], $key)]
            ?? 69; // fallback: Necklaces & Pendants
    }

    private function sheetToCategory(string $sheet): string
    {
        $map = [
            'Accessories (AC)' => 'accessories',
            'Earrings(E)'      => 'earrings',
            'Rings(R)'         => 'rings',
            'Necklace(N)'      => 'necklace',
            'Bracelet(BR)'     => 'bracelet',
            'Bangles(BA)'      => 'bangles',
            'Pendent set(PS)'  => 'pendant set',
            'Nose pins(NP)'    => 'nose pin',
            'Anklets(A)'       => 'anklet',
        ];
        return $map[$sheet] ?? 'accessories';
    }

    private function generateName(string $category, string $itemCode, string $sheet): string
    {
        $prefixes = [
            'earrings'    => 'Elegant Earrings',
            'earings'     => 'Elegant Earrings',
            'rings'       => 'Fashion Ring',
            'ring'        => 'Fashion Ring',
            'necklace'    => 'Statement Necklace',
            'bangles'     => 'Gold Bangle Set',
            'bangle'      => 'Gold Bangle Set',
            'bracelet'    => 'Gold Bracelet Set',
            'pendant set' => 'Pendant Jewellery Set',
            'pendent set' => 'Pendant Jewellery Set',
            'nose pin'    => 'Gold Nose Pin',
            'nose pins'   => 'Gold Nose Pin',
            'anklet'      => 'Gold Anklet',
            'anklets'     => 'Gold Anklet',
            'accessories' => 'Jewellery Gift Set',
        ];

        $key    = strtolower(trim($category));
        $prefix = $prefixes[$key] ?? ucwords($category ?: $sheet);
        return $prefix . ' – ' . strtoupper($itemCode);
    }

    private function defaultPrice(string $category): float
    {
        // Default INR prices when USD price is missing
        $defaults = [
            'bangles'     => 799,
            'bangle'      => 799,
            'nose pin'    => 499,
            'anklet'      => 699,
            'accessories' => 1299,
        ];
        return $defaults[strtolower(trim($category))] ?? 899;
    }

    private function toInr(float $usd): float
    {
        return round($usd * $this->usdRate, 2);
    }

    private function price(mixed $val): float
    {
        $str = trim((string)($val ?? ''));
        $str = preg_replace('/[^\d.]/', '', $str);
        return $str !== '' ? (float)$str : 0.0;
    }

    private function val(mixed $v): string
    {
        return trim((string)($v ?? ''));
    }

    private function cleanText(string $text): string
    {
        // Remove zero-width spaces and normalize
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);
        return trim($text);
    }

    private function makeShortDesc(string $description, string $name): string
    {
        $clean = $this->cleanText($description);
        if (empty($clean)) {
            return "Premium quality {$name}. Elegant design crafted for everyday wear and special occasions.";
        }
        // Take first sentence or first 160 chars
        $firstSentence = Str::before($clean, '.');
        return Str::limit(empty($firstSentence) ? $clean : $firstSentence . '.', 200);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function buildAttributes(string $color, ?string $sizes): array
    {
        $attrs = [];
        if (!empty($color)) {
            $attrs[] = ['name' => 'Color', 'value' => ucwords(strtolower($color))];
        }
        if (!empty($sizes) && strtolower($sizes) !== 'one size' && strtolower($sizes) !== 'sizes details') {
            $attrs[] = ['name' => 'Size', 'value' => $sizes];
        }
        return $attrs;
    }

    private function buildSpecs(string $color, ?string $sizes, string $itemNo = ''): array
    {
        $specs = [
            'Material'  => 'Stainless Steel with Gold Plating',
            'Finish'    => 'Gold-Plated',
        ];
        if (!empty($color)) {
            $specs['Color'] = ucwords(strtolower($color));
        }
        if (!empty($sizes) && strtolower($sizes) !== 'one size' && strtolower($sizes) !== 'sizes details') {
            $specs['Available Sizes'] = $sizes;
        }
        if (!empty($itemNo)) {
            $specs['Supplier Code'] = $itemNo;
        }
        return $specs;
    }
}
