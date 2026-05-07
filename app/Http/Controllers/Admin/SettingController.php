<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SettingController extends Controller
{
    public function general(): View
    {
        $settings = Setting::whereIn('group', ['general', 'store'])->pluck('value', 'key');

        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name'          => 'required|string|max:255',
            'site_tagline'       => 'nullable|string|max:255',
            'site_email'         => 'required|email',
            'site_phone'         => 'nullable|string|max:20',
            'site_address'       => 'nullable|string|max:500',
            'timezone'           => 'required|string',
            'date_format'        => 'required|string',
            'currency'           => 'required|string|size:3',
            'currency_symbol'    => 'required|string|max:5',
            'currency_position'  => 'required|in:before,after',
            'exchange_rate_inr'  => 'nullable|numeric|min:0',
        ]);

        // Save exchange rate: use manually entered value, or auto-fetch from live API
        $code    = strtolower($request->input('currency', 'inr'));
        $rateKey = 'exchange_rate_' . $code;
        if ($code !== 'inr') {
            $manualRate = $request->filled('exchange_rate_inr') ? (float) $request->input('exchange_rate_inr') : 0;
            $rate = $manualRate > 0 ? $manualRate : $this->fetchLiveRate(strtoupper($code));
            if ($rate > 0) {
                Setting::updateOrCreate(['key' => $rateKey], ['value' => (string) $rate, 'group' => 'general']);
                Cache::forget("setting.{$rateKey}");
            }
        }
        unset($validated['exchange_rate_inr']);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'general']
            );
            Cache::forget("setting.{$key}");
        }
        Cache::forget('currency_config');
        Cache::forget('settings.all');
        Cache::forget('settings.group.general');

        $rateMsg = ($code !== 'inr') ? ' Exchange rate auto-updated.' : '';
        return back()->with('success', 'General settings updated successfully.' . $rateMsg);
    }

    public function payment(): View
    {
        $settings = Setting::where('group', 'payment')->pluck('value', 'key');

        return view('admin.settings.payment', compact('settings'));
    }

    public function updatePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'paypal_client_id'    => 'nullable|string|max:255',
            'paypal_client_secret' => 'nullable|string|max:255',
            'paypal_mode'         => 'nullable|in:sandbox,live',
            'stripe_publishable_key' => 'nullable|string|max:255',
            'stripe_secret_key'      => 'nullable|string|max:255',
            'stripe_webhook_secret'  => 'nullable|string|max:255',
        ]);

        // Boolean toggles — use request->boolean() so unchecked checkboxes save '0'
        foreach (['paypal_enabled', 'stripe_enabled'] as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->boolean($key) ? '1' : '0', 'group' => 'payment']
            );
        }

        // Credential / text fields — skip blank secrets to keep existing value
        foreach ($validated as $key => $value) {
            if (in_array($key, ['paypal_client_secret', 'stripe_secret_key', 'stripe_webhook_secret']) && empty($value)) {
                continue; // Keep existing secret
            }
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '', 'group' => 'payment']
            );
        }

        Cache::forget('settings.all');

        return back()->with('success', 'Payment settings updated successfully.');
    }

    public function shipping(): View
    {
        $settings = Setting::where('group', 'shipping')->pluck('value', 'key');

        return view('admin.settings.shipping', compact('settings'));
    }

    public function updateShipping(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'free_shipping_enabled' => 'boolean',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'flat_rate_enabled' => 'boolean',
            'flat_rate_amount' => 'nullable|numeric|min:0',
            'local_pickup_enabled' => 'boolean',
            'local_pickup_address' => 'nullable|string|max:500',
            'shipping_origin_country' => 'required|string|size:2',
            'shipping_origin_state' => 'nullable|string',
            'shipping_origin_zip' => 'nullable|string|max:20',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '', 'group' => 'shipping']
            );
        }

        return back()->with('success', 'Shipping settings updated successfully.');
    }

    public function tax(): View
    {
        $settings = Setting::where('group', 'tax')->pluck('value', 'key');

        return view('admin.settings.tax', compact('settings'));
    }

    public function updateTax(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tax_enabled' => 'boolean',
            'tax_calculation' => 'in:exclusive,inclusive',
            'tax_based_on' => 'in:billing,shipping,store',
            'tax_display_cart' => 'in:excluding,including',
            'tax_display_checkout' => 'in:excluding,including',
            'tax_round_at_subtotal' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '', 'group' => 'tax']
            );
        }

        return back()->with('success', 'Tax settings updated successfully.');
    }

    public function email(): View
    {
        $settings = Setting::where('group', 'email')->pluck('value', 'key');

        return view('admin.settings.email', compact('settings'));
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mail_driver' => 'required|in:smtp,sendmail,mailgun,ses,postmark',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'mail_password' && empty($value)) {
                continue; // Keep existing password
            }
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '', 'group' => 'email']
            );
        }

        Cache::forget('settings.all');

        return back()->with('success', 'Email settings updated successfully.');
    }

    public function seo(): View
    {
        $settings = Setting::where('group', 'seo')->pluck('value', 'key');

        return view('admin.settings.seo', compact('settings'));
    }

    public function updateSeo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'og_image' => 'nullable|string',
            'google_analytics_id' => 'nullable|string',
            'google_tag_manager_id' => 'nullable|string',
            'facebook_pixel_id' => 'nullable|string',
            'robots_txt' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '', 'group' => 'seo']
            );
        }

        return back()->with('success', 'SEO settings updated successfully.');
    }

    public function productCard(): View
    {
        $settings = Setting::whereIn('group', ['product_card', 'features'])->pluck('value', 'key');

        $defaults = [
            'product_card_quick_view' => '1',
            'product_card_add_to_cart' => '1',
            'product_card_wishlist' => '1',
            'support_tickets_enabled' => '1',
        ];

        foreach ($defaults as $key => $default) {
            if (!isset($settings[$key])) {
                $settings[$key] = $default;
            }
        }

        return view('admin.settings.product-card', compact('settings'));
    }

    public function testEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        try {
            $settings = Setting::where('group', 'email')->pluck('value', 'key');

            config([
                'mail.mailers.smtp.host'       => $settings['mail_host'] ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port'       => $settings['mail_port'] ?? config('mail.mailers.smtp.port'),
                'mail.mailers.smtp.encryption' => $settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
                'mail.mailers.smtp.username'   => $settings['mail_username'] ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password'   => $settings['mail_password'] ?? config('mail.mailers.smtp.password'),
                'mail.from.address'            => $settings['mail_from_address'] ?? config('mail.from.address'),
                'mail.from.name'               => $settings['mail_from_name'] ?? config('mail.from.name'),
            ]);

            \Illuminate\Support\Facades\Mail::raw(
                'This is a test email from your store. SMTP is configured correctly!',
                function ($message) use ($request, $settings) {
                    $message->to($request->email)
                        ->subject('Test Email — ' . ($settings['mail_from_name'] ?? 'Your Store'));
                }
            );

            return response()->json(['success' => true, 'message' => 'Test email sent to ' . $request->email]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()]);
        }
    }

    public function importTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->setCellValue('A1', 'group');
        $sheet->setCellValue('B1', 'key');
        $sheet->setCellValue('C1', 'value');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(50);

        // Example rows
        $examples = [
            ['general', 'site_name', 'My Store'],
            ['general', 'site_email', 'admin@example.com'],
            ['email', 'mail_host', 'smtp.gmail.com'],
            ['email', 'mail_port', '587'],
            ['email', 'mail_encryption', 'tls'],
            ['email', 'mail_username', 'you@gmail.com'],
            ['email', 'mail_from_address', 'noreply@example.com'],
            ['email', 'mail_from_name', 'My Store'],
            ['seo', 'meta_title', 'My Store – Best Products'],
            ['payment', 'stripe_enabled', '1'],
        ];

        $row = 2;
        foreach ($examples as $example) {
            $sheet->setCellValue("A{$row}", $example[0]);
            $sheet->setCellValue("B{$row}", $example[1]);
            $sheet->setCellValue("C{$row}", $example[2]);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'settings-import-template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        // Lift limits for large uploads at runtime
        @ini_set('memory_limit', '512M');
        @ini_set('max_execution_time', '300');

        $request->validate([
            'file' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['xlsx', 'xls'])) {
                        $fail('Only .xlsx and .xls files are allowed.');
                    }
                },
            ],
        ]);

        try {
            $path = $request->file('file')->getRealPath();

            // Use chunk read filter to keep memory low for large files
            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);

            // Read only first row to detect columns
            $reader->setReadFilter(new class(1, 1) implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
                public function __construct(private int $from, private int $to) {}
                public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool {
                    return $row >= $this->from && $row <= $this->to;
                }
            });

            $headerSheet   = $reader->load($path)->getActiveSheet();
            $headerRowData = $headerSheet->toArray(null, true, true, true)[1] ?? [];
            $header        = array_map('strtolower', array_map('trim', array_map(fn($v) => (string) $v, $headerRowData)));

            if (!in_array('group', $header) || !in_array('key', $header) || !in_array('value', $header)) {
                return response()->json(['success' => false, 'message' => 'Invalid format. First row must have columns: group, key, value.']);
            }

            $colGroup = array_search('group', $header);
            $colKey   = array_search('key', $header);
            $colValue = array_search('value', $header);

            // Now read all data rows in chunks of 500
            $chunkSize     = 500;
            $startRow      = 2;
            $imported      = 0;
            $skipped       = 0;
            $importedRows  = [];
            $protectedKeys = ['mail_password', 'paypal_client_secret', 'stripe_secret_key', 'stripe_webhook_secret'];
            $hasMore       = true;

            while ($hasMore) {
                $endRow = $startRow + $chunkSize - 1;

                $reader->setReadFilter(new class($startRow, $endRow) implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
                    public function __construct(private int $from, private int $to) {}
                    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool {
                        return $row >= $this->from && $row <= $this->to;
                    }
                });

                $chunkSheet = $reader->load($path)->getActiveSheet();
                $chunkRows  = $chunkSheet->toArray(null, true, true, true);

                // Filter only rows in range (sheet toArray returns all cached rows)
                $dataRows = array_filter($chunkRows, fn($i) => $i >= $startRow && $i <= $endRow, ARRAY_FILTER_USE_KEY);

                if (empty($dataRows)) { break; }

                foreach ($dataRows as $row) {
                    $group = trim((string) ($row[$colGroup] ?? ''));
                    $key   = trim((string) ($row[$colKey] ?? ''));
                    $value = trim((string) ($row[$colValue] ?? ''));

                    if (empty($group) || empty($key)) { $skipped++; continue; }
                    if (in_array($key, $protectedKeys) && empty($value)) { $skipped++; continue; }

                    Setting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value, 'group' => $group]
                    );
                    Cache::forget("setting.{$key}");
                    $imported++;
                    $importedRows[] = ['group' => $group, 'key' => $key, 'value' => $value];
                }

                $hasMore  = count($dataRows) >= $chunkSize;
                $startRow = $endRow + 1;
                unset($chunkSheet, $chunkRows, $dataRows);
            }

            if ($imported === 0 && $skipped === 0) {
                return response()->json(['success' => false, 'message' => 'No data rows found in the file.']);
            }

            Cache::forget('settings.all');

            return response()->json([
                'success'  => true,
                'message'  => "Import complete: {$imported} setting(s) updated.",
                'detail'   => $skipped > 0 ? "{$skipped} row(s) skipped (empty key/group)." : null,
                'imported' => $imported,
                'rows'     => $importedRows,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to read file: ' . $e->getMessage()]);
        }
    }

    public function updateProductCard(Request $request): RedirectResponse
    {
        $productCardFields = ['product_card_quick_view', 'product_card_add_to_cart', 'product_card_wishlist'];
        foreach ($productCardFields as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->boolean($key) ? '1' : '0', 'type' => 'boolean', 'group' => 'product_card']
            );
            Cache::forget("setting.{$key}");
        }

        $featureFields = ['support_tickets_enabled'];
        foreach ($featureFields as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->boolean($key) ? '1' : '0', 'type' => 'boolean', 'group' => 'features']
            );
            Cache::forget("setting.{$key}");
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Fetch live exchange rate for the given currency code (e.g. "USD" → 1.25 means 1 USD = 1.25 of base currency).
     */
    private function fetchLiveRate(string $code): float
    {
        try {
            $json = @file_get_contents(
                "https://open.er-api.com/v6/latest/GBP",
                false,
                stream_context_create(['http' => ['timeout' => 5]])
            );
            if ($json) {
                $data = json_decode($json, true);
                // data['rates'] gives "how many X per 1 GBP"
                // We want "how many GBP per 1 X" → invert
                if (!empty($data['rates'][$code])) {
                    return round(1 / (float) $data['rates'][$code], 6);
                }
            }
        } catch (\Throwable) {}

        // Fallback hardcoded rates (GBP per 1 unit)
        $fallback = [
            'USD' => 1.27, 'EUR' => 1.17,
            'AED' => 4.67, 'SGD' => 1.71, 'CAD' => 1.75,
            'AUD' => 1.96, 'JPY' => 192.0,
        ];
        return $fallback[$code] ?? 0.0;
    }
}
