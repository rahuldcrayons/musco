<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Category;
use App\Models\Product;

class ImportExcelProducts extends Command
{
    protected $signature = 'import:excel-products {file? : Path to xlsx file}';
    protected $description = 'Import products and categories from MU & co.xlsx';

    private array $sheetConfigs = [
        'Accessories (AC)' => ['category' => 'Accessories', 'cols' => [4, 5, 7, 8, 10, 11, 3, 1]],
        'Earrings(E)'      => ['category' => 'Earrings',    'cols' => [4, 5, 7, 8, 10, 11, 3, 1]],
        'Rings(R)'         => ['category' => 'Rings',       'cols' => [4, 5, 7, 8, 10, 11, 3, 1]],
        'Necklace(N)'      => ['category' => 'Necklace',    'cols' => [4, 5, 7, 8, 10, 11, 3, 1]],
        'Bracelet(BR)'     => ['category' => 'Bracelet',    'cols' => [4, 5, 7, 8, 10, 11, 3, 1]],
        'Bangles(BA)'      => ['category' => 'Bangles',     'cols' => [4, 5, 7, 8, 10, 11, 3, 1]],
        'Pendent set(PS)'  => ['category' => 'Pendant Set', 'cols' => [4, 5, 7, 8, 10, 11, 3, 1]],
        'Nose pins(NP)'    => ['category' => 'Nose Pins',   'cols' => [4, 5, 7, 8, 10, 11, 3, 1]],
        'Anklets(A)'       => ['category' => 'Anklets',     'cols' => [4, 5, 7, 8, 10, 11, 3, 1]],
    ];

    public function handle(): int
    {
        $file = $this->argument('file') ?? base_path('MU & co.xlsx');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $this->info("Loading: $file");
        $spreadsheet = IOFactory::load($file);

        $categoryCache = [];
        $imported = 0;
        $skipped = 0;

        $this->importSheet1($spreadsheet, $categoryCache, $imported, $skipped);

        foreach ($this->sheetConfigs as $sheetName => $config) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (!$sheet) {
                $this->warn("Sheet not found: $sheetName");
                continue;
            }

            $rows = $sheet->toArray();
            $category = $this->getOrCreateCategory($config['category'], $categoryCache);
            $cols = $config['cols'];

            foreach (array_slice($rows, 1) as $row) {
                if (empty(array_filter($row))) continue;

                $itemCode = trim((string)($row[$cols[6]] ?? ''));
                if (!$itemCode) continue;

                if (Product::where('sku', $itemCode)->exists()) {
                    $skipped++;
                    continue;
                }

                $name        = trim((string)($row[$cols[0]] ?? ''));
                $description = trim((string)($row[$cols[1]] ?? ''));
                $price       = (float)($row[$cols[2]] ?? 0);
                $mrp         = (float)($row[$cols[3]] ?? 0);
                $stock       = (int)($row[$cols[4]] ?? 0);
                $color       = trim((string)($row[$cols[5]] ?? ''));
                $itemNo      = trim((string)($row[$cols[7]] ?? ''));

                if (!$name) {
                    $name = $config['category'] . ' ' . strtoupper($itemCode);
                }

                // barcode has a unique constraint; make it unique by appending item code
                $barcode = $itemNo ? $itemNo . '-' . $itemCode : null;

                $this->createProduct([
                    'category_id'    => $category->id,
                    'name'           => $name,
                    'description'    => $description,
                    'price'          => $price > 0 ? $price : 0,
                    'mrp'            => $mrp > 0 ? $mrp : null,
                    'stock_quantity' => $stock,
                    'metal_color'    => $color ?: null,
                    'sku'            => $itemCode,
                    'barcode'        => $barcode,
                ]);

                $imported++;
            }

            $this->info("  {$config['category']}: done");
        }

        $this->info("Import complete. Imported: $imported, Skipped (duplicates): $skipped");
        return 0;
    }

    private function importSheet1(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet, array &$cache, int &$imported, int &$skipped): void
    {
        $sheet = $spreadsheet->getSheetByName('Sheet1');
        if (!$sheet) return;

        $rows = $sheet->toArray();

        $categoryMap = [
            'pendant set' => 'Pendant Set',
            'pendent set' => 'Pendant Set',
            'earrings'    => 'Earrings',
            'necklace'    => 'Necklace',
            'bracelet'    => 'Bracelet',
            'rings'       => 'Rings',
            'bangles'     => 'Bangles',
            'accessories' => 'Accessories',
            'nose pin'    => 'Nose Pins',
            'anklet'      => 'Anklets',
        ];

        // Header row is row index 1; data starts at index 2
        foreach (array_slice($rows, 2) as $row) {
            if (empty(array_filter($row))) continue;

            $name        = trim((string)($row[1] ?? ''));
            $categoryStr = strtolower(trim((string)($row[2] ?? '')));
            $mrp         = (float)($row[4] ?? 0);
            $color       = trim((string)($row[5] ?? ''));
            $price       = (float)($row[6] ?? 0);
            $stock       = (int)($row[7] ?? 0);
            $statusStr   = strtolower(trim((string)($row[8] ?? '')));
            $description = trim((string)($row[9] ?? ''));

            if (!$name || !$categoryStr) continue;

            $slug = Str::slug($name);
            if (Product::where('slug', $slug)->exists()) {
                $skipped++;
                continue;
            }

            $categoryName = $categoryMap[$categoryStr] ?? ucwords($categoryStr);
            $category = $this->getOrCreateCategory($categoryName, $cache);

            $this->createProduct([
                'category_id'    => $category->id,
                'name'           => $name,
                'description'    => $description,
                'price'          => $price > 0 ? $price : 0,
                'mrp'            => $mrp > 0 ? $mrp : null,
                'stock_quantity' => $stock,
                'metal_color'    => $color ?: null,
                'is_active'      => str_contains($statusStr, 'stock'),
                'sku'            => 'MC-' . strtoupper(Str::random(8)),
            ]);

            $imported++;
        }

        $this->info("  Sheet1: done");
    }

    private function getOrCreateCategory(string $name, array &$cache): Category
    {
        if (isset($cache[$name])) {
            return $cache[$name];
        }

        $category = Category::firstOrCreate(
            ['slug' => Str::slug($name)],
            [
                'name'      => $name,
                'is_active' => true,
                'level'     => 0,
                'position'  => 0,
                'path'      => Str::slug($name),
            ]
        );

        $cache[$name] = $category;
        return $cache[$name];
    }

    private function createProduct(array $data): void
    {
        $name = $data['name'];
        $slug = Str::slug($name);
        $base = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "$base-$i";
            $i++;
        }

        Product::create([
            'name'           => $name,
            'slug'           => $slug,
            'category_id'    => $data['category_id'] ?? null,
            'description'    => $data['description'] ?? null,
            'price'          => $data['price'] ?? 0,
            'mrp'            => $data['mrp'] ?? ($data['price'] ?? 0),
            'cost_price'     => null,
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'stock_status'   => ($data['stock_quantity'] ?? 0) > 0 ? 'in_stock' : 'out_of_stock',
            'metal_color'    => $data['metal_color'] ?? null,
            'sku'            => $data['sku'] ?? ('MC-' . strtoupper(Str::random(8))),
            'barcode'        => $data['barcode'] ?? null,
            'is_active'      => $data['is_active'] ?? true,
            'is_featured'    => false,
            'is_taxable'     => false,
            'published_at'   => now(),
        ]);
    }
}
