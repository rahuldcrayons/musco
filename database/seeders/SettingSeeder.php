<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['group' => 'general', 'key' => 'site_name', 'value' => 'Trendimus', 'type' => 'string'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Exquisite Jewellery for Every Occasion', 'type' => 'string'],
            ['group' => 'general', 'key' => 'site_email', 'value' => 'support@trendimus.com', 'type' => 'string'],
            ['group' => 'general', 'key' => 'site_phone', 'value' => '+91 98765 43210', 'type' => 'string'],
            ['group' => 'general', 'key' => 'site_address', 'value' => 'Mumbai, Maharashtra, India', 'type' => 'string'],
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Asia/Kolkata', 'type' => 'string'],
            ['group' => 'general', 'key' => 'date_format', 'value' => 'M d, Y', 'type' => 'string'],
            ['group' => 'general', 'key' => 'currency', 'value' => 'INR', 'type' => 'string'],
            ['group' => 'general', 'key' => 'currency_symbol', 'value' => '₹', 'type' => 'string'],
            ['group' => 'general', 'key' => 'currency_position', 'value' => 'before', 'type' => 'string'],
            ['group' => 'general', 'key' => 'announcement_text', 'value' => 'Free Shipping on Orders Above ₹999 | BIS Hallmarked Jewellery | 15-Day Easy Returns', 'type' => 'string'],
            ['group' => 'general', 'key' => 'footer_about', 'value' => 'Your trusted destination for certified gold, diamond & silver jewellery. Hallmarked collections, exquisite craftsmanship, and timeless designs for every occasion.', 'type' => 'string'],

            // Payment Settings
            ['group' => 'payment', 'key' => 'stripe_enabled', 'value' => '0', 'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'paypal_enabled', 'value' => '0', 'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'paypal_mode', 'value' => 'sandbox', 'type' => 'string'],
            ['group' => 'payment', 'key' => 'cod_enabled', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'upi_enabled', 'value' => '1', 'type' => 'boolean'],

            // Shipping Settings
            ['group' => 'shipping', 'key' => 'free_shipping_enabled', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'shipping', 'key' => 'free_shipping_threshold', 'value' => '499', 'type' => 'integer'],
            ['group' => 'shipping', 'key' => 'flat_rate_enabled', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'shipping', 'key' => 'flat_rate_amount', 'value' => '49', 'type' => 'string'],
            ['group' => 'shipping', 'key' => 'local_pickup_enabled', 'value' => '0', 'type' => 'boolean'],
            ['group' => 'shipping', 'key' => 'shipping_origin_country', 'value' => 'IN', 'type' => 'string'],

            // Tax Settings
            ['group' => 'tax', 'key' => 'tax_enabled', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'tax', 'key' => 'tax_calculation', 'value' => 'inclusive', 'type' => 'string'],
            ['group' => 'tax', 'key' => 'tax_based_on', 'value' => 'shipping', 'type' => 'string'],
            ['group' => 'tax', 'key' => 'tax_display_cart', 'value' => 'including', 'type' => 'string'],

            // Email Settings
            ['group' => 'email', 'key' => 'mail_driver', 'value' => 'smtp', 'type' => 'string'],
            ['group' => 'email', 'key' => 'mail_from_address', 'value' => 'noreply@trendimus.com', 'type' => 'string'],
            ['group' => 'email', 'key' => 'mail_from_name', 'value' => 'Trendimus', 'type' => 'string'],

            // SEO Settings
            ['group' => 'seo', 'key' => 'meta_title', 'value' => 'Trendimus - Exquisite Jewellery for Every Occasion', 'type' => 'string'],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => 'Shop certified gold, diamond & silver jewellery at Trendimus. Rings, necklaces, earrings, bangles & more. BIS hallmarked with free shipping across India.', 'type' => 'string'],
            ['group' => 'seo', 'key' => 'meta_keywords', 'value' => 'gold jewellery, diamond rings, silver jewellery, necklaces, earrings, bangles, engagement rings, wedding jewellery, hallmarked, India', 'type' => 'string'],

            // Social
            ['group' => 'social', 'key' => 'social_facebook', 'value' => '#', 'type' => 'string'],
            ['group' => 'social', 'key' => 'social_instagram', 'value' => '#', 'type' => 'string'],
            ['group' => 'social', 'key' => 'social_twitter', 'value' => '#', 'type' => 'string'],
            ['group' => 'social', 'key' => 'social_youtube', 'value' => '#', 'type' => 'string'],

            // Business Hours & WhatsApp
            ['group' => 'general', 'key' => 'business_hours', 'value' => 'Mon - Sat: 10AM - 7PM', 'type' => 'string'],
            ['group' => 'general', 'key' => 'whatsapp_number', 'value' => '919354567705', 'type' => 'string'],
            ['group' => 'general', 'key' => 'whatsapp_message', 'value' => 'Hi Trendimus! I have a question about your products.', 'type' => 'string'],
            ['group' => 'general', 'key' => 'whatsapp_status_text', 'value' => 'Typically replies instantly', 'type' => 'string'],

            // Trust Badges
            ['group' => 'storefront', 'key' => 'dispatch_time', 'value' => '24 hours', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'return_policy_days', 'value' => '7', 'type' => 'integer'],
            ['group' => 'storefront', 'key' => 'trust_badge_1_title', 'value' => '24 hours Dispatch', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'trust_badge_1_desc', 'value' => 'Fast shipping', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'trust_badge_2_title', 'value' => 'Easy Returns', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'trust_badge_2_desc', 'value' => '7-day hassle-free', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'trust_badge_3_title', 'value' => 'Secure Payment', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'trust_badge_3_desc', 'value' => '100% safe & encrypted', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'trust_badge_4_title', 'value' => 'Quality Assured', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'trust_badge_4_desc', 'value' => '100% genuine products', 'type' => 'string'],

            // Exit Intent Popup
            ['group' => 'storefront', 'key' => 'exit_popup_discount', 'value' => '10', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'exit_popup_headline', 'value' => "Wait! Don't leave yet", 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'exit_popup_button_text', 'value' => 'GET 10% OFF', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'exit_popup_success_message', 'value' => 'Check your email! Your 10% discount code is on its way.', 'type' => 'string'],

            // Chatbot Widget
            ['group' => 'storefront', 'key' => 'chatbot_greeting', 'value' => 'Hi there! 👋', 'type' => 'string'],
            ['group' => 'storefront', 'key' => 'chatbot_welcome_message', 'value' => "I'm your shopping assistant. Ask me about products, orders, sizes, offers, or anything about the store!", 'type' => 'string'],
        ];

        foreach ($settings as $settingData) {
            Setting::updateOrCreate(
                ['group' => $settingData['group'], 'key' => $settingData['key']],
                $settingData
            );
        }
    }
}
