<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

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
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_email' => 'required|email',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:500',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'currency' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:5',
            'currency_position' => 'required|in:before,after',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'general']
            );
            Cache::forget("setting.{$key}");
        }
        Cache::forget('currency_config');

        return back()->with('success', 'General settings updated successfully.');
    }

    public function payment(): View
    {
        $settings = Setting::where('group', 'payment')->pluck('value', 'key');

        return view('admin.settings.payment', compact('settings'));
    }

    public function updatePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'razorpay_key_id'     => 'nullable|string|max:255',
            'razorpay_key_secret' => 'nullable|string|max:255',
            'razorpay_mode'       => 'nullable|in:test,live',
            'cod_instructions'    => 'nullable|string|max:1000',
        ]);

        // Boolean toggles — use request->boolean() so unchecked checkboxes save '0'
        foreach (['razorpay_enabled', 'upi_enabled', 'cod_enabled'] as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->boolean($key) ? '1' : '0', 'group' => 'payment']
            );
        }

        // Credential / text fields — skip blank secrets to keep existing value
        foreach ($validated as $key => $value) {
            if ($key === 'razorpay_key_secret' && empty($value)) {
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
}
