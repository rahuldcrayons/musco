<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportMuscoExcel extends Command
{
    protected $signature   = 'musco:import-products {file} {--fresh : Delete ALL existing products first}';
    protected $description = 'Import products from MU & co.xlsx (single-sheet format)';

    // Excel column C category name → category ID
    private array $catMap = [
        'pendant set'  => 38,
        'pendant sets' => 38,
        'necklace'     => 37,
        'necklaces'    => 37,
        'earings'      => 39,  // sic — matches Excel typo
        'earrings'     => 39,
        'rings'        => 36,
        'ring'         => 36,
    ];

    // category_id → image filename prefix (storage/app/public/products/)
    private array $imgPrefix = [
        38 => ['prefix' => 'ps', 'ext' => 'png'],
        37 => ['prefix' => 'n',  'ext' => 'png'],
        39 => ['prefix' => 'e',  'ext' => 'png'],
        36 => ['prefix' => 'r',  'ext' => 'png'],
    ];

    // category_id → SKU prefix
    private array $skuPrefix = [
        38 => 'PS',
        37 => 'N',
        39 => 'E',
        36 => 'R',
    ];

    public function handle(): int
    {
        $file = $this->argument('file');
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        // ── Step 1: Fresh wipe ───────────────────────────────────────────
        if ($this->option('fresh')) {
            $this->warn('--fresh: Removing ALL existing products and images...');
            DB::table('product_images')->delete();
            DB::table('product_variants')->whereExists(function ($q) {
                $q->from('products')->whereColumn('products.id', 'product_variants.product_id');
            })->delete();
            Product::withTrashed()->forceDelete();
            $this->info('  All products deleted.');
        }

        // ── Step 2: Load Excel ───────────────────────────────────────────
        $this->info("Loading: $file");
        $spreadsheet = IOFactory::load($file);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true);

        // Row 2 = headers, rows 3+ = data
        $data = [];
        foreach ($rows as $i => $row) {
            if ($i < 3) continue;
            $name = $this->clean($row['B'] ?? '');
            if (empty($name)) continue;
            $data[] = [
                'name'    => $name,
                'cat_raw' => strtolower(trim($row['C'] ?? '')),
                'size'    => trim($row['D'] ?? ''),
                'mrp'     => (float) ($row['E'] ?? 0),
                'color'   => ucfirst(strtolower(trim($row['F'] ?? ''))),
                'price'   => (float) ($row['G'] ?? 0),
                'stock'   => (int) ($row['H'] ?? 0),
                'status'  => strtolower(trim($row['I'] ?? 'in stock')),
                'desc'    => $this->cleanDesc(trim($row['J'] ?? '')),
            ];
        }

        $this->info('Found ' . count($data) . ' products in Excel.');

        // ── Step 3: Import products ──────────────────────────────────────
        $skuCounters = [];  // catId → counter
        $imgCounters = [];  // catId → counter
        $created     = 0;

        foreach ($data as $row) {
            $catId = $this->catMap[$row['cat_raw']] ?? null;
            if (!$catId) {
                $this->warn("  Unknown category: '{$row['cat_raw']}' for \"{$row['name']}\" — skipped");
                continue;
            }

            // Generate sequential SKU
            $skuCounters[$catId] = ($skuCounters[$catId] ?? 0) + 1;
            $prefix = $this->skuPrefix[$catId];
            $sku    = $prefix . str_pad($skuCounters[$catId], 3, '0', STR_PAD_LEFT);

            // Unique slug
            $baseSlug = Str::slug($row['name']);
            $slug     = $baseSlug;
            $attempt  = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . (++$attempt);
            }

            $inStock  = !str_contains($row['status'], 'out');
            $price    = $row['price'];
            $mrp      = $row['mrp'];
            $isActive = $price > 0;

            $product = Product::create([
                'name'              => $row['name'],
                'slug'              => $slug,
                'sku'               => $sku,
                'category_id'       => $catId,
                'price'             => $price,
                'mrp'               => $mrp > 0 ? $mrp : ($price > 0 ? round($price * 1.3, 2) : 0),
                'stock_quantity'    => $row['stock'],
                'stock_status'      => $inStock && $row['stock'] > 0 ? 'in_stock' : 'out_of_stock',
                'description'       => $row['desc'],
                'short_description' => $row['desc'] ? Str::limit(strip_tags($row['desc']), 200) : '',
                'metal_color'       => $row['color'],
                'is_active'         => $isActive,
                'status'            => $isActive ? 'approved' : 'draft',
            ]);

            // Attach image (sequential per category)
            $imgCounters[$catId] = ($imgCounters[$catId] ?? 0) + 1;
            $this->attachImage($product, $catId, $imgCounters[$catId]);

            $created++;
            $flag = $isActive ? '✓' : '○';
            $this->line("  $flag $sku | {$row['name']} | £{$price}");
        }

        $this->info('');
        $this->info("Done! Imported: $created products.");
        $this->info('Total products: ' . Product::count());

        return 0;
    }

    private function attachImage(Product $product, int $catId, int $seq): void
    {
        if (!isset($this->imgPrefix[$catId])) return;

        $cfg  = $this->imgPrefix[$catId];
        $exts = [$cfg['ext'], $cfg['ext'] === 'png' ? 'jpg' : 'png', 'jpeg'];

        foreach ($exts as $ext) {
            $filename = "products/{$cfg['prefix']}{$seq}.{$ext}";
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
                $this->line("    → image: $filename");
                return;
            }
        }
        $this->warn("    ⚠ No image for {$cfg['prefix']}{$seq}.*");
    }

    private function clean(string $s): string
    {
        // Remove zero-width spaces and other invisible unicode chars, trim
        return trim(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s));
    }

    private function cleanDesc(string $desc): string
    {
        // Replace double-slash separators with paragraph breaks
        $desc = str_replace('//', "\n\n", $desc);
        return trim($desc);
    }
}
