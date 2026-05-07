<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportMuscoV2 extends Command
{
    protected $signature = 'musco:import-v2
        {file : Path to cleaned MU & co Excel file}
        {--dry-run : Preview counts without writing to database}
        {--fresh : Delete ALL existing products first}
        {--rate= : Manual USD→INR exchange rate (overrides live rate)}';

    protected $description = 'Import MU & co product catalog from Excel into the database (Sheet1 + all category sheets)';

    // Excel category value → DB category slug (actual slugs from production DB)
    private array $categorySlugMap = [
        'pendant set'          => 'jewellery-pendant-sets',
        'pendant sets'         => 'jewellery-pendant-sets',
        'necklaces'            => 'jewellery-necklaces',
        'necklace'             => 'jewellery-necklaces',
        'earrings'             => 'jewellery-earrings',
        'rings'                => 'jewellery-rings',
        'accessories'          => 'accessories',
        'bracelets'            => 'jewellery-bracelets',
        'bracelet'             => 'jewellery-bracelets',
        'bangles'              => 'jewellery-bangles-only',
        'nose pins'            => 'jewellery-nose-pins',
        'nose pin'             => 'jewellery-nose-pins',
        'anklets'              => 'jewellery-anklets',
        'anklet'               => 'jewellery-anklets',
    ];

    // Sheet name → DB category slug
    private array $sheetCategoryMap = [
        'Accessories (AC)'  => 'accessories',
        'Earrings(E)'       => 'jewellery-earrings',
        'Rings(R)'          => 'jewellery-rings',
        'Necklace(N)'       => 'jewellery-necklaces',
        'Bracelet(BR)'      => 'jewellery-bracelets',
        'Bangles(BA)'       => 'jewellery-bangles-only',
        'Pendent set(PS)'   => 'jewellery-pendant-sets',
        'Nose pins(NP)'     => 'jewellery-nose-pins',
        'Anklets(A)'        => 'jewellery-anklets',
    ];

    // SKU prefix per category slug
    private array $skuPrefixMap = [
        'jewellery-pendant-sets'   => 'PS',
        'jewellery-necklaces'      => 'N',
        'jewellery-earrings'       => 'E',
        'jewellery-rings'          => 'R',
        'accessories'              => 'AC',
        'jewellery-bracelets'      => 'BR',
        'jewellery-bangles-only'   => 'BA',
        'jewellery-nose-pins'      => 'NP',
        'jewellery-anklets'        => 'AN',
    ];

    // Default USD prices per category slug (used when category sheet rows have no price)
    private array $defaultPrices = [
        'jewellery-earrings'       => ['price' => 7.99,  'mrp' => 14.99],
        'jewellery-rings'          => ['price' => 4.49,  'mrp' => 9.99],
        'jewellery-necklaces'      => ['price' => 7.99,  'mrp' => 16.99],
        'jewellery-bracelets'      => ['price' => 8.99,  'mrp' => 14.99],
        'jewellery-bangles-only'   => ['price' => 9.99,  'mrp' => 17.99],
        'jewellery-pendant-sets'   => ['price' => 8.99,  'mrp' => 16.99],
        'jewellery-nose-pins'      => ['price' => 5.99,  'mrp' => 11.99],
        'jewellery-anklets'        => ['price' => 5.99,  'mrp' => 11.99],
        'accessories'              => ['price' => 19.99, 'mrp' => 29.99],
    ];

    // Category image folder prefix (storage/app/public/products/)
    private array $imgPrefixMap = [
        'jewellery-pendant-sets'   => 'ps',
        'jewellery-necklaces'      => 'n',
        'jewellery-earrings'       => 'e',
        'jewellery-rings'          => 'r',
        'accessories'              => 'ac',
        'jewellery-bracelets'      => 'br',
        'jewellery-bangles-only'   => 'ba',
        'jewellery-nose-pins'      => 'np',
        'jewellery-anklets'        => 'an',
    ];

    // runtime state
    private array $categoryCache = [];   // slug → id
    private float $usdRate        = 0;
    private bool  $dryRun         = false;
    private array $stats          = [];

    public function handle(): int
    {
        $file = $this->argument('file');
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $this->dryRun = (bool) $this->option('dry-run');
        if ($this->dryRun) {
            $this->warn('DRY RUN — no data will be written.');
        }

        // ── Exchange rate ────────────────────────────────────────────────
        $rateOpt = $this->option('rate');
        $this->usdRate = $rateOpt ? (float) $rateOpt : (float) usd_to_inr(1);
        if ($this->usdRate <= 0) {
            $this->usdRate = 84.0; // safe fallback
        }
        $this->info(sprintf('USD→INR rate: %.2f', $this->usdRate));

        // ── Category cache ───────────────────────────────────────────────
        $categories = Category::where('is_active', true)->get(['id', 'slug']);
        foreach ($categories as $cat) {
            $this->categoryCache[$cat->slug] = $cat->id;
        }
        $this->info('Loaded ' . count($this->categoryCache) . ' categories.');

        // ── Fresh wipe ───────────────────────────────────────────────────
        if ($this->option('fresh') && !$this->dryRun) {
            $this->warn('--fresh: Removing ALL existing products and images…');
            DB::table('product_images')->delete();
            Product::withTrashed()->forceDelete();
            $this->info('  All products deleted.');
        }

        // ── Load spreadsheet ─────────────────────────────────────────────
        $this->info("Loading spreadsheet…");
        $spreadsheet = IOFactory::load($file);

        // ── Pass 1: Sheet1 ───────────────────────────────────────────────
        $this->info('');
        $this->info('═══ PASS 1: Sheet1 (master list) ═══');
        $this->stats['sheet1'] = ['created' => 0, 'skipped' => 0, 'errors' => 0];

        $sheet1 = $spreadsheet->getSheetByName('Sheet1');
        if ($sheet1) {
            $this->processSheet1($sheet1);
        } else {
            $this->warn('Sheet1 not found — skipping pass 1.');
        }

        // ── Pass 2: Category sheets ──────────────────────────────────────
        $this->info('');
        $this->info('═══ PASS 2: Category sheets ═══');

        foreach ($this->sheetCategoryMap as $sheetName => $catSlug) {
            $this->stats[$sheetName] = ['created' => 0, 'skipped' => 0, 'errors' => 0];
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (!$sheet) {
                $this->warn("  Sheet '$sheetName' not found — skipping.");
                continue;
            }
            $this->info("  ▸ $sheetName → $catSlug");
            $this->processCategorySheet($sheet, $sheetName, $catSlug);
        }

        // ── Summary ──────────────────────────────────────────────────────
        $this->info('');
        $this->info('═══ SUMMARY ═══');
        $totalCreated = $totalSkipped = $totalErrors = 0;
        foreach ($this->stats as $pass => $s) {
            $this->line(sprintf(
                '  %-25s created: %3d  skipped: %3d  errors: %3d',
                $pass, $s['created'], $s['skipped'], $s['errors']
            ));
            $totalCreated += $s['created'];
            $totalSkipped += $s['skipped'];
            $totalErrors  += $s['errors'];
        }
        $this->info(str_repeat('─', 60));
        $this->info(sprintf('  TOTAL: created=%d  skipped=%d  errors=%d', $totalCreated, $totalSkipped, $totalErrors));

        if (!$this->dryRun) {
            $this->info('Total products in DB: ' . Product::count());
        }

        return 0;
    }

    // ────────────────────────────────────────────────────────────────────
    // Pass 1 — Sheet1
    // Columns (row 1 = headers, data from row 2):
    //   A=S No  B=Product Name  C=Category  D=Sizes  E=Compare Price
    //   F=Color  G=Selling Price  H=Stock  I=Status  J=Description  K=Image
    // ────────────────────────────────────────────────────────────────────
    private function processSheet1(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $rows       = $sheet->toArray(null, true, true, true);
        $skuCounter = []; // catSlug → counter

        foreach ($rows as $i => $row) {
            if ($i < 2) continue; // row 1 = headers

            $name = $this->clean($row['B'] ?? '');
            if (empty($name)) {
                $this->stats['sheet1']['skipped']++;
                continue;
            }

            $catRaw  = strtolower(trim($row['C'] ?? ''));
            $catSlug = $this->categorySlugMap[$catRaw] ?? null;
            if (!$catSlug || !isset($this->categoryCache[$catSlug])) {
                $this->warn("    ⚠ Unknown category '$catRaw' for \"$name\" — skipped");
                $this->stats['sheet1']['skipped']++;
                continue;
            }
            $catId = $this->categoryCache[$catSlug];

            // SKU
            $prefix = $this->skuPrefixMap[$catSlug] ?? 'MU';
            $skuCounter[$catSlug] = ($skuCounter[$catSlug] ?? 0) + 1;
            $sku = $prefix . '-' . str_pad($skuCounter[$catSlug], 3, '0', STR_PAD_LEFT);

            // Dedupe
            if (Product::where('sku', $sku)->exists()) {
                $this->line("    ↷ SKU $sku exists — skipped");
                $this->stats['sheet1']['skipped']++;
                continue;
            }

            // Prices (USD → INR)
            $priceUsd = (float) ($row['G'] ?? 0);
            $mrpUsd   = (float) ($row['E'] ?? 0);
            $price    = round($priceUsd * $this->usdRate, 2);
            $mrp      = $mrpUsd > 0 ? round($mrpUsd * $this->usdRate, 2) : round($price * 1.35, 2);
            if ($mrp <= $price) {
                $mrp = round($price * 1.35, 2);
            }

            $stock      = max(0, (int) ($row['H'] ?? 0));
            $statusRaw  = strtolower(trim($row['I'] ?? 'in stock'));
            $inStock    = !str_contains($statusRaw, 'out') && $stock > 0;
            $color      = ucfirst(strtolower($this->clean($row['F'] ?? '')));
            $size       = $this->clean($row['D'] ?? '');
            $desc       = $this->cleanDesc($this->clean($row['J'] ?? ''));

            $specs = array_filter(['color' => $color, 'size' => $size]);

            if ($this->dryRun) {
                $this->line("    [DRY] $sku | $name | ₹$price");
                $this->stats['sheet1']['created']++;
                continue;
            }

            try {
                $product = Product::create([
                    'name'              => $name,
                    'sku'               => $sku,
                    'category_id'       => $catId,
                    'price'             => $price,
                    'mrp'               => $mrp,
                    'stock_quantity'    => $stock,
                    'stock_status'      => $inStock ? 'in_stock' : 'out_of_stock',
                    'description'       => $desc,
                    'short_description' => $desc ? Str::limit(strip_tags($desc), 200) : '',
                    'metal_color'       => $color,
                    'specifications'    => $specs ?: null,
                    'is_active'         => $price > 0,
                    'is_taxable'        => true,
                    'tax_rate'          => 18.00,
                    'status'            => $price > 0 ? 'approved' : 'draft',
                    'published_at'      => $price > 0 ? now() : null,
                ]);

                $this->attachImageByPrefix($product, $catSlug, $skuCounter[$catSlug]);
                $this->line("    ✓ $sku | $name | ₹$price");
                $this->stats['sheet1']['created']++;
            } catch (\Throwable $e) {
                $this->error("    ✗ $name: " . $e->getMessage());
                $this->stats['sheet1']['errors']++;
            }
        }
    }

    // ────────────────────────────────────────────────────────────────────
    // Pass 2 — Category sheets
    // Columns (row 1 = headers, data from row 2):
    //   A=S No  B=Item No  C=Category  D=Item Code  E=Product Name
    //   F=Description  G=Images  H=Listing Price  I=Compare Price
    //   J=Offer Price  K=Stock  L=Color  M=Status  N=Size (Bangles)
    // ────────────────────────────────────────────────────────────────────
    private function processCategorySheet(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $sheetName,
        string $catSlug
    ): void {
        $catId = $this->categoryCache[$catSlug] ?? null;
        if (!$catId) {
            $this->warn("    ⚠ Category slug '$catSlug' not in DB — skipped");
            $this->stats[$sheetName]['skipped']++;
            return;
        }

        $rows      = $sheet->toArray(null, true, true, true);
        $defaults  = $this->defaultPrices[$catSlug] ?? ['price' => 9.99, 'mrp' => 16.99];
        $catName   = $this->slugToCategoryName($catSlug);

        foreach ($rows as $i => $row) {
            if ($i < 2) continue; // row 1 = headers

            $itemNo   = $this->clean($row['B'] ?? '');
            $itemCode = $this->clean($row['D'] ?? '');
            $name     = $this->clean($row['E'] ?? '');

            // Skip completely empty rows
            if (empty($itemCode) && empty($name)) {
                $this->stats[$sheetName]['skipped']++;
                continue;
            }

            // Fallback name
            if (empty($name)) {
                $name = trim("$catName $itemCode");
            }

            $sku     = 'MU-' . strtoupper($itemCode);
            $barcode = ($itemNo && $itemCode) ? ($itemNo . '-' . $itemCode) : null;

            // Dedupe: check SKU OR barcode
            $exists = Product::where('sku', $sku)->exists()
                || ($barcode && Product::where('barcode', $barcode)->exists());
            if ($exists) {
                $this->line("    ↷ $sku already exists — skipped");
                $this->stats[$sheetName]['skipped']++;
                continue;
            }

            // Prices
            $priceUsd = (float) ($row['H'] ?? 0);
            $mrpUsd   = (float) ($row['I'] ?? 0);
            $costUsd  = (float) ($row['J'] ?? 0);

            if ($priceUsd <= 0) $priceUsd = $defaults['price'];
            if ($mrpUsd   <= 0) $mrpUsd   = $defaults['mrp'];

            $price    = round($priceUsd * $this->usdRate, 2);
            $mrp      = round($mrpUsd   * $this->usdRate, 2);
            $costPrice = $costUsd > 0 ? round($costUsd * $this->usdRate, 2) : null;

            if ($mrp <= $price) {
                $mrp = round($price * 1.35, 2);
            }

            $stock      = max(0, (int) ($row['K'] ?? 2));
            $statusRaw  = strtolower(trim($row['M'] ?? 'in stock'));
            $inStock    = !str_contains($statusRaw, 'out') && $stock > 0;
            $color      = ucfirst(strtolower($this->clean($row['L'] ?? '')));
            $desc       = $this->cleanDesc($this->clean($row['F'] ?? ''));
            $size       = $this->clean($row['N'] ?? ''); // Bangles sheet only

            $specs = array_filter([
                'color'         => $color,
                'size'          => $size,
                'supplier_code' => $itemNo,
            ]);

            if ($this->dryRun) {
                $this->line("    [DRY] $sku | $name | ₹$price");
                $this->stats[$sheetName]['created']++;
                continue;
            }

            try {
                $product = Product::create([
                    'name'              => $name,
                    'sku'               => $sku,
                    'barcode'           => $barcode,
                    'category_id'       => $catId,
                    'price'             => $price,
                    'mrp'               => $mrp,
                    'cost_price'        => $costPrice,
                    'stock_quantity'    => $stock,
                    'stock_status'      => $inStock ? 'in_stock' : 'out_of_stock',
                    'description'       => $desc,
                    'short_description' => $desc ? Str::limit(strip_tags($desc), 200) : '',
                    'metal_color'       => $color,
                    'specifications'    => $specs ?: null,
                    'is_active'         => $price > 0,
                    'is_taxable'        => true,
                    'tax_rate'          => 18.00,
                    'status'            => $price > 0 ? 'approved' : 'draft',
                    'published_at'      => $price > 0 ? now() : null,
                ]);

                $seqNum = $this->stats[$sheetName]['created'] + 1;
                $this->attachImageByPrefix($product, $catSlug, $seqNum);
                $this->line("    ✓ $sku | $name | ₹$price");
                $this->stats[$sheetName]['created']++;
            } catch (\Throwable $e) {
                $this->error("    ✗ $name: " . $e->getMessage());
                $this->stats[$sheetName]['errors']++;
            }
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function attachImageByPrefix(Product $product, string $catSlug, int $seq): void
    {
        $prefix = $this->imgPrefixMap[$catSlug] ?? null;
        if (!$prefix) return;

        foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
            $filename = "products/{$prefix}{$seq}.{$ext}";
            $fullPath = storage_path("app/public/{$filename}");
            if (file_exists($fullPath)) {
                DB::table('product_images')->insert([
                    'product_id' => $product->id,
                    'url'        => $filename,
                    'alt_text'   => $product->name,
                    'position'   => 1,
                    'is_primary' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->line("      → image: $filename");
                return;
            }
        }
        // No image found — product is still created, just without an image
    }

    private function clean(string $s): string
    {
        // Remove zero-width spaces and other invisible unicode chars
        return trim(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s));
    }

    private function cleanDesc(string $desc): string
    {
        if (empty($desc)) return '';
        // Normalise multi-slash separators → paragraph break
        $desc = preg_replace('/\/{2,}/', "\n\n", $desc);
        // Literal '\n' text → actual newline
        $desc = str_replace('\n', "\n", $desc);
        return trim($desc);
    }

    private function slugToCategoryName(string $slug): string
    {
        $map = [
            'jewellery-pendant-sets' => 'Pendant Set',
            'jewellery-necklaces'    => 'Necklace',
            'jewellery-earrings'     => 'Earrings',
            'jewellery-rings'        => 'Rings',
            'accessories'            => 'Accessories',
            'jewellery-bracelets'    => 'Bracelets',
            'jewellery-bangles-only' => 'Bangles',
            'jewellery-nose-pins'    => 'Nose Pins',
            'jewellery-anklets'      => 'Anklets',
        ];
        return $map[$slug] ?? ucwords(str_replace('-', ' ', $slug));
    }
}
