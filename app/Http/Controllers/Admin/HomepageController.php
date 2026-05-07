<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\HomepageSection;
use App\Models\NavigationMenu;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HomepageController extends Controller
{
    public function index()
    {
        $sections = HomepageSection::ordered()->get();
        $banners = Banner::where('position', 'hero')->ordered()->get();
        $testimonials = Testimonial::ordered()->get();

        return view('admin.homepage.index', compact('sections', 'banners', 'testimonials'));
    }

    // Site Settings (Logo, Brand Name, etc.)
    public function siteSettings()
    {
        $settings = [
            'site_logo' => Setting::get('site_logo', ''),
            'site_name' => Setting::get('site_name', 'Trendymus'),
            'site_tagline' => Setting::get('site_tagline', 'Unlock Your Natural Beauty'),
            'site_description' => Setting::get('site_description', ''),
            'footer_about' => Setting::get('footer_about', ''),
            'footer_copyright' => Setting::get('footer_copyright', ''),
            'social_facebook' => Setting::get('social_facebook', ''),
            'social_instagram' => Setting::get('social_instagram', ''),
            'social_twitter' => Setting::get('social_twitter', ''),
            'social_youtube' => Setting::get('social_youtube', ''),
            'social_tiktok' => Setting::get('social_tiktok', ''),
            'social_pinterest' => Setting::get('social_pinterest', ''),
            'contact_email' => Setting::get('contact_email', ''),
            'contact_phone' => Setting::get('contact_phone', ''),
            'contact_address' => Setting::get('contact_address', ''),
            'announcement_text' => Setting::get('announcement_text', ''),
            // Homepage display counts
            'homepage_featured_count' => Setting::get('homepage_featured_count', 10),
            'homepage_new_arrivals_count' => Setting::get('homepage_new_arrivals_count', 10),
            'homepage_bestsellers_count' => Setting::get('homepage_bestsellers_count', 10),
            'homepage_deals_count' => Setting::get('homepage_deals_count', 10),
            'homepage_testimonials_count' => Setting::get('homepage_testimonials_count', 6),
            'homepage_new_arrivals_days' => Setting::get('homepage_new_arrivals_days', 30),
        ];

        return view('admin.homepage.site-settings', compact('settings'));
    }

    public function updateSiteSettings(Request $request)
    {
        $fields = [
            'site_name', 'site_tagline', 'site_description',
            'footer_about', 'footer_copyright',
            'social_facebook', 'social_instagram', 'social_twitter',
            'social_youtube', 'social_tiktok', 'social_pinterest',
            'contact_email', 'contact_phone', 'contact_address',
            'announcement_text',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field), 'string', 'homepage');
            }
        }

        // Homepage display count settings
        $integerFields = [
            'homepage_featured_count',
            'homepage_new_arrivals_count',
            'homepage_bestsellers_count',
            'homepage_deals_count',
            'homepage_testimonials_count',
            'homepage_new_arrivals_days',
        ];

        foreach ($integerFields as $field) {
            if ($request->has($field)) {
                Setting::set($field, (int) $request->input($field), 'integer', 'homepage');
            }
        }

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('branding', 'public');
            Setting::set('site_logo', $path, 'string', 'homepage');
        }

        Cache::flush();

        return back()->with('success', 'Site settings updated successfully.');
    }

    // About Page Settings
    public function aboutSettings()
    {
        $settings = [
            'about_image' => Setting::get('about_image', ''),
            'about_heading' => Setting::get('about_heading', ''),
            'about_paragraph_1' => Setting::get('about_paragraph_1', ''),
            'about_paragraph_2' => Setting::get('about_paragraph_2', ''),
            'about_usp_1_title' => Setting::get('about_usp_1_title', ''),
            'about_usp_1_desc' => Setting::get('about_usp_1_desc', ''),
            'about_usp_2_title' => Setting::get('about_usp_2_title', ''),
            'about_usp_2_desc' => Setting::get('about_usp_2_desc', ''),
            'about_usp_3_title' => Setting::get('about_usp_3_title', ''),
            'about_usp_3_desc' => Setting::get('about_usp_3_desc', ''),
            'about_card_1_title' => Setting::get('about_card_1_title', ''),
            'about_card_1_desc' => Setting::get('about_card_1_desc', ''),
            'about_card_1_image' => Setting::get('about_card_1_image', ''),
            'about_card_2_title' => Setting::get('about_card_2_title', ''),
            'about_card_2_desc' => Setting::get('about_card_2_desc', ''),
            'about_card_2_image' => Setting::get('about_card_2_image', ''),
            'about_card_3_title' => Setting::get('about_card_3_title', ''),
            'about_card_3_desc' => Setting::get('about_card_3_desc', ''),
            'about_card_3_image' => Setting::get('about_card_3_image', ''),
            'about_why_subtitle' => Setting::get('about_why_subtitle', ''),
            'about_faq_1_title' => Setting::get('about_faq_1_title', ''),
            'about_faq_1_content' => Setting::get('about_faq_1_content', ''),
            'about_faq_2_title' => Setting::get('about_faq_2_title', ''),
            'about_faq_2_content' => Setting::get('about_faq_2_content', ''),
            'about_faq_3_title' => Setting::get('about_faq_3_title', ''),
            'about_faq_3_content' => Setting::get('about_faq_3_content', ''),
            'about_faq_4_title' => Setting::get('about_faq_4_title', ''),
            'about_faq_4_content' => Setting::get('about_faq_4_content', ''),
        ];

        return view('admin.homepage.about-settings', compact('settings'));
    }

    public function updateAboutSettings(Request $request)
    {
        $fields = [
            'about_heading', 'about_paragraph_1', 'about_paragraph_2',
            'about_usp_1_title', 'about_usp_1_desc',
            'about_usp_2_title', 'about_usp_2_desc',
            'about_usp_3_title', 'about_usp_3_desc',
            'about_card_1_title', 'about_card_1_desc',
            'about_card_2_title', 'about_card_2_desc',
            'about_card_3_title', 'about_card_3_desc',
            'about_why_subtitle',
            'about_faq_1_title', 'about_faq_1_content',
            'about_faq_2_title', 'about_faq_2_content',
            'about_faq_3_title', 'about_faq_3_content',
            'about_faq_4_title', 'about_faq_4_content',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field), 'string', 'homepage');
            }
        }

        // File uploads
        $imageFields = [
            'about_image' => 'about',
            'about_card_1_image' => 'about',
            'about_card_2_image' => 'about',
            'about_card_3_image' => 'about',
        ];

        foreach ($imageFields as $field => $folder) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store($folder, 'public');
                Setting::set($field, $path, 'string', 'homepage');
            }
        }

        Cache::flush();

        return back()->with('success', 'About page settings updated successfully.');
    }

    // Hero Banners
    public function heroBanners()
    {
        $banners = Banner::where('position', 'hero')->ordered()->get();
        return view('admin.homepage.hero-banners', compact('banners'));
    }

    public function storeHeroBanner(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'image' => 'required|image|max:5120',
            'link' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'overlay_style' => 'nullable|string|in:' . implode(',', array_keys(Banner::OVERLAY_STYLES)),
        ]);

        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'name' => $request->name,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'button_text' => $request->button_text,
            'image_url' => $imagePath,
            'link' => $request->link,
            'overlay_style' => $request->overlay_style ?? 'left-dark',
            'position' => 'hero',
            'priority' => Banner::where('position', 'hero')->max('priority') + 1,
            'is_active' => true,
        ]);

        Cache::flush();

        return back()->with('success', 'Hero banner added successfully.');
    }

    public function updateHeroBanner(Request $request, Banner $banner)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:5120',
            'link' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'overlay_style' => 'nullable|string|in:' . implode(',', array_keys(Banner::OVERLAY_STYLES)),
        ]);

        $data = $request->only(['name', 'title', 'subtitle', 'button_text', 'link', 'overlay_style']);

        if ($request->hasFile('image')) {
            if ($banner->image_url) {
                Storage::disk('public')->delete($banner->image_url);
            }
            $data['image_url'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);
        Cache::flush();

        return back()->with('success', 'Hero banner updated successfully.');
    }

    public function deleteHeroBanner(Banner $banner)
    {
        if ($banner->image_url) {
            Storage::disk('public')->delete($banner->image_url);
        }
        $banner->delete();
        Cache::flush();

        return back()->with('success', 'Hero banner deleted successfully.');
    }

    public function reorderHeroBanners(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:banners,id',
        ]);

        foreach ($request->order as $position => $id) {
            Banner::where('id', $id)->update(['priority' => $position]);
        }

        Cache::flush();

        return response()->json(['success' => true]);
    }

    public function toggleHeroBanner(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        Cache::flush();
        return back()->with('success', 'Banner status updated.');
    }

    // Homepage Sections
    public function sections()
    {
        $sections = HomepageSection::ordered()->get();
        return view('admin.homepage.sections', compact('sections'));
    }

    public function editSection(HomepageSection $section)
    {
        return view('admin.homepage.edit-section', compact('section'));
    }

    public function updateSection(Request $request, HomepageSection $section)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['title', 'subtitle', 'button_text', 'button_link']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->has('background_color')) {
            $data['background_color'] = $request->input('background_color');
        }

        if ($request->has('text_color')) {
            $data['text_color'] = $request->input('text_color');
        }

        if ($request->has('content')) {
            $data['content'] = $request->input('content');
        }

        if ($request->hasFile('image')) {
            if ($section->image_url) {
                Storage::disk('public')->delete($section->image_url);
            }
            $data['image_url'] = $request->file('image')->store('sections', 'public');
        }

        $section->update($data);
        Cache::flush();

        return back()->with('success', 'Section updated successfully.');
    }

    public function toggleSection(HomepageSection $section)
    {
        $section->update(['is_active' => !$section->is_active]);
        Cache::flush();
        return back()->with('success', 'Section visibility updated.');
    }

    public function reorderSections(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:homepage_sections,id',
        ]);

        foreach ($request->order as $position => $id) {
            HomepageSection::where('id', $id)->update(['position' => $position]);
        }

        Cache::flush();
        return response()->json(['success' => true]);
    }

    // Testimonials
    public function testimonials()
    {
        $testimonials = Testimonial::ordered()->get();
        return view('admin.homepage.testimonials', compact('testimonials'));
    }

    public function storeTestimonial(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'product_name' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'title', 'content', 'rating', 'product_name']);
        $data['position'] = Testimonial::max('position') + 1;
        $data['is_active'] = true;

        if ($request->hasFile('avatar')) {
            $data['avatar_url'] = $request->file('avatar')->store('testimonials', 'public');
        }

        Testimonial::create($data);

        return back()->with('success', 'Testimonial added successfully.');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'product_name' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'title', 'content', 'rating', 'product_name']);

        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar_url) {
                Storage::disk('public')->delete($testimonial->avatar_url);
            }
            $data['avatar_url'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $testimonial->update($data);

        return back()->with('success', 'Testimonial updated successfully.');
    }

    public function deleteTestimonial(Testimonial $testimonial)
    {
        if ($testimonial->avatar_url) {
            Storage::disk('public')->delete($testimonial->avatar_url);
        }
        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted successfully.');
    }

    public function toggleTestimonial(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);
        return back()->with('success', 'Testimonial visibility updated.');
    }

    // Navigation Menus
    public function navigation()
    {
        $headerMenus = NavigationMenu::getByLocation('header');
        $footer_col1 = NavigationMenu::getByLocation('footer_col1');
        $footer_col2 = NavigationMenu::getByLocation('footer_col2');
        $footer_col3 = NavigationMenu::getByLocation('footer_col3');

        return view('admin.homepage.navigation', compact('headerMenus', 'footer_col1', 'footer_col2', 'footer_col3'));
    }

    public function storeNavItem(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'label' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:navigation_menus,id',
        ]);

        NavigationMenu::create([
            'location' => $request->location,
            'label' => $request->label,
            'url' => $request->url,
            'parent_id' => $request->parent_id,
            'position' => NavigationMenu::where('location', $request->location)->max('position') + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'Menu item added successfully.');
    }

    public function updateNavItem(Request $request, NavigationMenu $menu)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'url' => 'required|string|max:255',
        ]);

        $menu->update($request->only(['label', 'url']));

        return back()->with('success', 'Menu item updated successfully.');
    }

    public function deleteNavItem(NavigationMenu $menu)
    {
        $menu->delete();
        return back()->with('success', 'Menu item deleted successfully.');
    }
}
