<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

if (!function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        return currency_config('symbol');
    }
}

if (!function_exists('account_url')) {
    /**
     * Generate an absolute URL that automatically applies the /account prefix for authenticated users.
     */
    function account_url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        $base = Auth::check() ? 'account' : '';

        if ($path === '') {
            return url($base ?: '/');
        }

        $fullPath = $base ? $base . '/' . $path : $path;

        return url($fullPath);
    }
}

if (!function_exists('currency_position')) {
    function currency_position(): string
    {
        return currency_config('position');
    }
}

if (!function_exists('format_price')) {
    function format_price(float|int|string|null $amount, int $decimals = 2): string
    {
        $config   = currency_config();
        $symbol   = $config['symbol'];
        $position = $config['position'];

        $floatAmount = (float) ($amount ?? 0);
        $formatted = number_format($floatAmount, 2);

        return $position === 'after'
            ? $formatted . $symbol
            : $symbol . $formatted;
    }
}

if (!function_exists('display_price_to_stored')) {
    /**
     * Convert a user-entered display price back to the stored (INR base) value.
     * Inverse of format_price: stored = display * exchange_rate (for non-INR currencies).
     * Used by filter controllers so price comparisons work in the DB's native unit.
     */
    function display_price_to_stored(float|int|string|null $displayPrice): float
    {
        return (float) ($displayPrice ?? 0);
    }
}

if (!function_exists('usd_to_gbp')) {
    /**
     * Convert USD amount to GBP.
     * Rate is fetched from exchangerate-api (free tier) and cached for 6 hours.
     * Falls back to a stored setting 'usd_gbp_rate', then to 0.79 if all fail.
     */
    function usd_to_gbp(float|int|string|null $usd, bool $formatted = false): float|string
    {
        $rate = Cache::remember('usd_gbp_rate', 21600, function () {
            try {
                $json = @file_get_contents('https://open.er-api.com/v6/latest/USD', false,
                    stream_context_create(['http' => ['timeout' => 5]])
                );
                if ($json) {
                    $data = json_decode($json, true);
                    if (isset($data['rates']['GBP'])) {
                        Setting::updateOrCreate(
                            ['key' => 'usd_gbp_rate'],
                            ['value' => (string) $data['rates']['GBP'], 'group' => 'currency']
                        );
                        return (float) $data['rates']['GBP'];
                    }
                }
            } catch (\Throwable) {}

            return (float) (Setting::get('usd_gbp_rate', '0.79'));
        });

        $gbp = round((float) ($usd ?? 0) * $rate, 2);
        return $formatted ? format_gbp($gbp) : $gbp;
    }
}

if (!function_exists('usd_to_inr')) {
    /** @deprecated Use usd_to_gbp() instead */
    function usd_to_inr(float|int|string|null $usd, bool $formatted = false): float|string
    {
        return usd_to_gbp($usd, $formatted);
    }
}

if (!function_exists('get_usd_gbp_rate')) {
    /**
     * Return the current USD→GBP exchange rate (cached 6 h).
     */
    function get_usd_gbp_rate(): float
    {
        return (float) Cache::remember('usd_gbp_rate', 21600, function () {
            return (float) (Setting::get('usd_gbp_rate', '0.79'));
        });
    }
}

if (!function_exists('get_usd_inr_rate')) {
    /** @deprecated Use get_usd_gbp_rate() instead */
    function get_usd_inr_rate(): float
    {
        return get_usd_gbp_rate();
    }
}

if (!function_exists('format_gbp')) {
    /**
     * Format a number as GBP using standard UK formatting.
     * e.g. format_gbp(1250000) → "£1,250,000.00"
     *      format_gbp(100)     → "£100.00"
     */
    function format_gbp(float|int|string|null $amount, bool $symbol = true): string
    {
        $num = (float) ($amount ?? 0);
        $formatted = number_format(abs($num), 2, '.', ',');
        $sign = $num < 0 ? '-' : '';
        return $symbol ? $sign . '£' . $formatted : $sign . $formatted;
    }
}

if (!function_exists('format_inr')) {
    /** @deprecated Use format_gbp() instead */
    function format_inr(float|int|string|null $amount, bool $symbol = true): string
    {
        return format_gbp($amount, $symbol);
    }
}

if (!function_exists('currency_config')) {
    function currency_config(string $key = null): mixed
    {
        $config = Cache::remember('currency_config', 3600, function () {
            return [
                'symbol' => Setting::get('currency_symbol', '£'),
                'position' => Setting::get('currency_position', 'before'),
                'code' => Setting::get('currency', 'GBP'),
            ];
        });

        return $key ? ($config[$key] ?? null) : $config;
    }
}
