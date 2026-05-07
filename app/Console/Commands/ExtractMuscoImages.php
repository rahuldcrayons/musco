<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class ExtractMuscoImages extends Command
{
    protected $signature   = 'trendymus:extract-images';
    protected $description = 'Extract embedded images from MU & co.xlsx and assign to products';

    private string $xlsxPath = 'D:/projects/trendymus/MU & co.xlsx';

    // Map sheet name → SKU prefix (for numbered products)
    private array $sheetPrefix = [
        'Accessories (AC)' => 'AC',
        'Earrings(E)'      => 'E',
        'Rings(R)'         => 'R',
        'Necklace(N)'      => 'N',
        'Bracelet(BR)'     => 'BR',
        'Bangles(BA)'      => 'BA',
        'Pendent set(PS)'  => 'PS',
        'Nose pins(NP)'    => 'NP',
        'Anklets(A)'       => 'A',
    ];

    public function handle(): int
    {
        // Ensure storage directory exists
        $dir = storage_path('app/public/products');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Open xlsx as ZIP for image extraction
        $zip = new ZipArchive();
        if ($zip->open($this->xlsxPath) !== true) {
            $this->error("Cannot open ZIP: {$this->xlsxPath}");
            return 1;
        }

        $this->info("Opened xlsx ZIP. Loading spreadsheet for drawing data...");

        $spreadsheet = IOFactory::load($this->xlsxPath);

        $totalImages = 0;
        $totalLinked = 0;

        // Remove all existing product_images for trendymus products (id >= 226)
        $trendymusIds = Product::where('id', '>=', 226)->pluck('id');
        $deleted  = ProductImage::whereIn('product_id', $trendymusIds)->delete();
        $this->info("Deleted {$deleted} old placeholder image records.");

        // ─── Sheet1 ───────────────────────────────────────────────────────────
        $sheet1Products = Product::where('sku', 'like', '%-S1')
            ->orderBy('id')
            ->pluck('id', 'sku')
            ->toArray();

        // Sheet1 products ordered by ID
        $sheet1Ids = array_values($sheet1Products);

        $sheet1 = $spreadsheet->getSheetByName('Sheet1');
        if ($sheet1) {
            foreach ($sheet1->getDrawingCollection() as $drawing) {
                $coord = $drawing->getCoordinates(); // e.g. "L3"
                $row   = (int) preg_replace('/[^0-9]/', '', $coord);
                // Row 3 = index 0, row 4 = index 1, etc. (first 2 rows skipped)
                $index = $row - 3;

                if ($index < 0 || $index >= count($sheet1Ids)) {
                    $this->warn("  Sheet1 drawing at {$coord} (index {$index}) — no matching product");
                    continue;
                }

                $productId = $sheet1Ids[$index];
                $imgPath   = $this->extractDrawingPath($drawing);

                if (!$imgPath) {
                    $this->warn("  Sheet1 {$coord} — could not determine image path");
                    continue;
                }

                $saved = $this->saveImage($zip, $imgPath, "sheet1-row{$row}");
                if ($saved) {
                    $this->linkImage($productId, $saved);
                    $totalImages++;
                    $totalLinked++;
                    $this->line("  Sheet1 {$coord} → product #{$productId} → {$saved}");
                }
            }
        }

        // ─── Other sheets ─────────────────────────────────────────────────────
        foreach ($spreadsheet->getSheetNames() as $idx => $sheetName) {
            if ($sheetName === 'Sheet1') continue;

            $prefix = $this->sheetPrefix[$sheetName] ?? null;
            if (!$prefix) {
                $this->warn("No prefix mapping for sheet: {$sheetName}");
                continue;
            }

            $sheet = $spreadsheet->getSheet($idx);

            // Detect header row (same logic as import command)
            $rows      = $sheet->toArray(null, true, true, true);
            $headerRow = null;
            foreach ($rows as $rowNum => $row) {
                $firstCell = strtolower(trim((string)($row['A'] ?? '')));
                if (in_array($firstCell, ['s no', 's. no.', 's.no', '1'])) {
                    $headerRow = $rowNum;
                    break;
                }
            }
            $dataStart = $headerRow ? $headerRow + 1 : 2;

            $drawings = $sheet->getDrawingCollection();
            if (count($drawings) === 0) {
                $this->line("  {$sheetName}: no images");
                continue;
            }

            $this->info("─── {$sheetName} ({$prefix}*) — " . count($drawings) . " images ───");

            foreach ($drawings as $drawing) {
                $coord = $drawing->getCoordinates(); // e.g. "G2"
                $row   = (int) preg_replace('/[^0-9]/', '', $coord);

                // Map row to SKU index: dataStart = first data row → index 1
                $num = $row - $dataStart + 1;
                $sku = $prefix . $num;

                $product = Product::where('sku', $sku)->first();
                if (!$product) {
                    $this->warn("  {$sheetName} {$coord} → SKU {$sku} not found in DB");
                    continue;
                }

                $imgPath = $this->extractDrawingPath($drawing);
                if (!$imgPath) {
                    $this->warn("  {$sheetName} {$coord} → SKU {$sku} — could not determine image path");
                    continue;
                }

                $saved = $this->saveImage($zip, $imgPath, strtolower($sku));
                if ($saved) {
                    $this->linkImage($product->id, $saved);
                    $totalImages++;
                    $totalLinked++;
                    $this->line("  {$coord} → {$sku} (#{$product->id}) → {$saved}");
                }
            }
        }

        $zip->close();

        $this->newLine();
        $this->info("✓ Done — Images extracted: {$totalImages}, products linked: {$totalLinked}");
        return 0;
    }

    /**
     * Extract the internal zip path from a drawing object.
     * Drawing path looks like: zip://D:\projects\trendymus\MU & co.xlsx#xl/media/image11.png
     * We need just the 'xl/media/image11.png' part.
     */
    private function extractDrawingPath(mixed $drawing): ?string
    {
        // Try getPath() which returns the zip:// URL
        try {
            $path = $drawing->getPath();
            if (str_contains($path, '#')) {
                return substr($path, strpos($path, '#') + 1);
            }
            // Sometimes it's just the media filename
            if (str_starts_with($path, 'xl/media/')) {
                return $path;
            }
        } catch (\Throwable) {}

        // Try getName() for older versions
        try {
            $name = $drawing->getName();
            if ($name && preg_match('/\.(png|jpg|jpeg|gif|bmp|webp)$/i', $name)) {
                return 'xl/media/' . $name;
            }
        } catch (\Throwable) {}

        return null;
    }

    /**
     * Extract image from ZIP and save to storage/app/public/products/
     * Returns the public URL path like 'products/ac1.jpg'
     */
    private function saveImage(ZipArchive $zip, string $zipPath, string $slug): ?string
    {
        $contents = $zip->getFromName($zipPath);
        if ($contents === false) {
            // Try with forward slashes normalized
            $contents = $zip->getFromName(str_replace('\\', '/', $zipPath));
        }
        if ($contents === false) {
            $this->warn("    Could not extract {$zipPath} from ZIP");
            return null;
        }

        // Determine extension from original path
        $ext      = strtolower(pathinfo($zipPath, PATHINFO_EXTENSION)) ?: 'jpg';
        $filename = $slug . '.' . $ext;
        $fullPath = storage_path('app/public/products/' . $filename);

        file_put_contents($fullPath, $contents);
        return 'products/' . $filename;
    }

    /**
     * Create product_images record linking product to its image.
     */
    private function linkImage(int $productId, string $url): void
    {
        ProductImage::create([
            'product_id' => $productId,
            'url'        => $url,
            'alt_text'   => null,
            'position'   => 0,
            'is_primary' => true,
        ]);
    }
}
