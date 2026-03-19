<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// CSRF Token Refresh (for long-lived POS sessions)
Route::get('/csrf-token', fn () => response()->json(['token' => csrf_token()]))->name('csrf-token');

// WhatsApp Webhook
Route::get('/webhook/whatsapp', [App\Http\Controllers\WhatsAppWebhookController::class, 'verify']);
Route::post('/webhook/whatsapp', [App\Http\Controllers\WhatsAppWebhookController::class, 'handle']);

// XML Sitemap
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-pages.xml', [App\Http\Controllers\SitemapController::class, 'pages']);
Route::get('/sitemap-products.xml', [App\Http\Controllers\SitemapController::class, 'products']);
Route::get('/sitemap-categories.xml', [App\Http\Controllers\SitemapController::class, 'categories']);
Route::get('/sitemap-brands.xml', [App\Http\Controllers\SitemapController::class, 'brands']);
Route::get('/sitemap-blog.xml', [App\Http\Controllers\SitemapController::class, 'blog']);

// Facebook Catalog Feed
Route::get('/feeds/facebook-catalog.xml', App\Http\Controllers\FacebookCatalogController::class)->name('facebook.catalog');
Route::get('/feeds/google-merchant.xml', App\Http\Controllers\GoogleMerchantController::class)->name('google.merchant');

// PWA Routes (served via Laravel when nginx doesn't serve static files through symlinks)
Route::get('/offline', fn () => view('offline'))->name('offline');
Route::get('/manifest.json', fn () => response()->file(public_path('manifest.json'), ['Content-Type' => 'application/manifest+json']));
Route::get('/sw.js', fn () => response()->file(public_path('sw.js'), ['Content-Type' => 'application/javascript', 'Service-Worker-Allowed' => '/']));

// Storefront Routes (cached for guest users)
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('cache.response:5');

// Products
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [App\Http\Controllers\ProductController::class, 'index'])->name('index')->middleware('cache.response:5');
    Route::get('/{product:slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('show')->middleware('cache.response:5');
});

// Alias for product show
Route::get('/product/{product:slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('product.show');

// Instagram Reels / Videos
Route::get('/reels', [App\Http\Controllers\ReelController::class, 'index'])->name('reels.index')->middleware('cache.response:5');
Route::get('/reels/{shortcode}', [App\Http\Controllers\ReelController::class, 'show'])->name('reels.show')->middleware('cache.response:5');
Route::get('/api/reels', [App\Http\Controllers\ReelController::class, 'apiLatest'])->name('reels.api')->middleware('cache.response:5');

// Quick View (AJAX)
Route::get('/product/{product}/quick-view', [App\Http\Controllers\ProductController::class, 'quickView'])->name('product.quick-view');

// Guest Reviews
Route::post('/products/{product}/guest-review', [App\Http\Controllers\GuestReviewController::class, 'store'])
    ->name('product.guest-review')
    ->middleware('throttle:3,60');

// Product Questions
Route::post('/products/{product}/ask-question', [App\Http\Controllers\ProductController::class, 'askQuestion'])
    ->name('product.ask-question')
    ->middleware('throttle:5,60');

// Categories
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [App\Http\Controllers\CategoryController::class, 'index'])->name('index')->middleware('cache.response:5');
    Route::get('/{category:slug}', [App\Http\Controllers\CategoryController::class, 'show'])->name('show')->middleware('cache.response:5');
});

// Alias for category show
Route::get('/category/{category:slug}', [App\Http\Controllers\CategoryController::class, 'show'])->name('category.show')->middleware('cache.response:5');

// Brands
Route::prefix('brands')->name('brands.')->group(function () {
    Route::get('/', [App\Http\Controllers\BrandController::class, 'index'])->name('index')->middleware('cache.response:5');
    Route::get('/{brand:slug}', [App\Http\Controllers\BrandController::class, 'show'])->name('show')->middleware('cache.response:5');
});

// Sellers
Route::get('/sellers/{seller:slug}', [App\Http\Controllers\SellerController::class, 'show'])->name('sellers.show');

// Search
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
Route::get('/search/suggestions', [App\Http\Controllers\SearchController::class, 'suggestions'])->name('search.suggestions');

// Special Pages
Route::get('/deals', [App\Http\Controllers\DealsController::class, 'index'])->name('deals');
Route::get('/new-arrivals', [App\Http\Controllers\ProductController::class, 'newArrivals'])->name('new-arrivals');
Route::get('/bestsellers', [App\Http\Controllers\ProductController::class, 'bestsellers'])->name('bestsellers');
Route::get('/wholesale', [App\Http\Controllers\WholesaleController::class, 'index'])->name('wholesale');

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/data', [App\Http\Controllers\CartController::class, 'data'])->name('data');
    Route::get('/', [App\Http\Controllers\CartController::class, 'index'])->name('index');
    Route::post('/add', [App\Http\Controllers\CartController::class, 'add'])->name('add');
    Route::put('/{cartItem}', [App\Http\Controllers\CartController::class, 'update'])->name('update');
    Route::delete('/{cartItem}', [App\Http\Controllers\CartController::class, 'destroy'])->name('destroy');
    Route::delete('/', [App\Http\Controllers\CartController::class, 'clear'])->name('clear');
    Route::post('/apply-coupon', [App\Http\Controllers\CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::delete('/remove-coupon', [App\Http\Controllers\CartController::class, 'removeCoupon'])->name('remove-coupon');
});

// Wishlist page (handles auth check in controller)
Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist');

// Wishlist actions (require auth)
Route::middleware('auth')->prefix('wishlist')->name('wishlist.')->group(function () {
    Route::post('/{product}', [App\Http\Controllers\WishlistController::class, 'store'])->name('store');
    Route::delete('/{product}', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('destroy');
});

// Guest Authentication Routes
Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Checkout (guest + auth)
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [App\Http\Controllers\CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [App\Http\Controllers\CheckoutController::class, 'process'])->name('process');
    Route::post('/razorpay/create-order', [App\Http\Controllers\CheckoutController::class, 'createRazorpayOrder'])->name('razorpay.create');
    Route::post('/razorpay/verify', [App\Http\Controllers\CheckoutController::class, 'verifyRazorpayPayment'])->name('razorpay.verify');
    Route::get('/success/{order}', [App\Http\Controllers\CheckoutController::class, 'success'])->name('success');
    Route::get('/failed', [App\Http\Controllers\CheckoutController::class, 'failed'])->name('failed');
});

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

    // Email Verification
    Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');

    // Account Routes
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [App\Http\Controllers\Account\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [App\Http\Controllers\Account\ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Account\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [App\Http\Controllers\Account\ProfileController::class, 'updatePassword'])->name('password.update');

        // Addresses
        Route::resource('addresses', App\Http\Controllers\Account\AddressController::class);

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [App\Http\Controllers\Account\OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [App\Http\Controllers\Account\OrderController::class, 'show'])->name('show');
            Route::post('/{order}/cancel', [App\Http\Controllers\Account\OrderController::class, 'cancel'])->name('cancel');
            Route::get('/{order}/invoice', [App\Http\Controllers\Account\OrderController::class, 'invoice'])->name('invoice');
            Route::get('/{order}/track', [App\Http\Controllers\Account\OrderController::class, 'track'])->name('track');
        });

        // Returns
        Route::resource('returns', App\Http\Controllers\Account\ReturnController::class);

        // Reviews
        Route::get('/reviews', [App\Http\Controllers\Account\ReviewController::class, 'index'])->name('reviews');
        Route::get('/reviews/create/{product}', [App\Http\Controllers\Account\ReviewController::class, 'create'])->name('reviews.create');
        Route::post('/reviews/{product}', [App\Http\Controllers\Account\ReviewController::class, 'store'])->name('reviews.store');

        // Support Tickets
        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/', [App\Http\Controllers\Account\TicketController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Account\TicketController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Account\TicketController::class, 'store'])->name('store');
            Route::get('/{ticket}', [App\Http\Controllers\Account\TicketController::class, 'show'])->name('show');
            Route::post('/{ticket}/reply', [App\Http\Controllers\Account\TicketController::class, 'reply'])->name('reply');
        });

        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Account\NotificationController::class, 'index'])->name('notifications');

        // Notification Preferences
        Route::get('/notification-preferences', [App\Http\Controllers\Account\NotificationPreferenceController::class, 'edit'])->name('notification-preferences');
        Route::put('/notification-preferences', [App\Http\Controllers\Account\NotificationPreferenceController::class, 'update'])->name('notification-preferences.update');

        // Become a Delivery Partner
        Route::get('/become-delivery-partner', [App\Http\Controllers\Account\DeliveryPartnerRegistrationController::class, 'create'])->name('become-delivery-partner');
        Route::post('/become-delivery-partner', [App\Http\Controllers\Account\DeliveryPartnerRegistrationController::class, 'store'])->name('become-delivery-partner.store');
        Route::post('/become-delivery-partner/documents', [App\Http\Controllers\Account\DeliveryPartnerRegistrationController::class, 'uploadDocuments'])->name('become-delivery-partner.documents');
    });
});

// Seller Registration (Guest)
Route::get('/sell', [App\Http\Controllers\Seller\RegistrationController::class, 'index'])->name('seller.register');
Route::post('/sell/register', [App\Http\Controllers\Seller\RegistrationController::class, 'store'])->name('seller.register.store');

// Newsletter
Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->middleware('throttle:5,1')->name('newsletter.subscribe');

// Push Notifications
Route::post('/push/subscribe', [App\Http\Controllers\PushSubscriptionController::class, 'subscribe'])->middleware('throttle:10,1')->name('push.subscribe');
Route::post('/push/unsubscribe', [App\Http\Controllers\PushSubscriptionController::class, 'unsubscribe'])->middleware('throttle:10,1')->name('push.unsubscribe');

// Recommendations (AJAX)
Route::prefix('recommendations')->name('recommendations.')->group(function () {
    Route::get('/recently-viewed', [App\Http\Controllers\Web\RecommendationController::class, 'recentlyViewed'])->name('recently-viewed');
    Route::get('/similar/{productId}', [App\Http\Controllers\Web\RecommendationController::class, 'similar'])->name('similar');
    Route::get('/bought-together/{productId}', [App\Http\Controllers\Web\RecommendationController::class, 'frequentlyBoughtTogether'])->name('bought-together');
    Route::get('/personalized', [App\Http\Controllers\Web\RecommendationController::class, 'personalized'])->name('personalized');
});

// AI Chatbot
Route::post('/chatbot/message', [App\Http\Controllers\ChatbotController::class, 'message'])->middleware('throttle:20,1')->name('chatbot.message');

// Track Order (Public with order number)
Route::get('/track-order', [App\Http\Controllers\TrackOrderController::class, 'index'])->name('track-order');
Route::post('/track-order', [App\Http\Controllers\TrackOrderController::class, 'track'])->name('track-order.track');

// Static/CMS Pages
Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('about');
Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('contact');
Route::post('/contact', [App\Http\Controllers\PageController::class, 'sendContact'])->middleware('throttle:5,1')->name('contact.send');
Route::get('/faq', [App\Http\Controllers\PageController::class, 'faq'])->name('faq');
Route::get('/offers', fn () => view('pages.offers'))->name('offers');
Route::get('/blog', [App\Http\Controllers\PageController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [App\Http\Controllers\PageController::class, 'blogShow'])->name('blog.show');
Route::get('/careers', [App\Http\Controllers\PageController::class, 'careers'])->name('careers');
Route::get('/help', [App\Http\Controllers\PageController::class, 'help'])->name('help');
Route::get('/returns-policy', [App\Http\Controllers\PageController::class, 'returns'])->name('returns');
Route::get('/shipping', [App\Http\Controllers\PageController::class, 'shipping'])->name('shipping');
Route::get('/size-guide', [App\Http\Controllers\PageController::class, 'sizeGuide'])->name('size-guide');
Route::get('/privacy-policy', [App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-service', [App\Http\Controllers\PageController::class, 'terms'])->name('terms');
Route::get('/cookie-policy', [App\Http\Controllers\PageController::class, 'cookiePolicy'])->name('cookie-policy');
Route::get('/gdpr', [App\Http\Controllers\PageController::class, 'gdpr'])->name('gdpr');
Route::get('/sitemap', [App\Http\Controllers\PageController::class, 'sitemap'])->name('sitemap.html');
Route::get('/page/{page:slug}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');

// Instagram Callbacks (Facebook App requirement)
Route::match(['get', 'post'], '/auth/instagram/callback', [\App\Http\Controllers\Api\InstagramCallbackController::class, 'deauthorize'])->name('instagram.callback')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
Route::match(['get', 'post'], '/auth/instagram/deauthorize', [\App\Http\Controllers\Api\InstagramCallbackController::class, 'deauthorize'])->name('instagram.deauthorize.web')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
Route::match(['get', 'post'], '/auth/instagram/delete', [\App\Http\Controllers\Api\InstagramCallbackController::class, 'delete'])->name('instagram.delete.web')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// Load Admin Routes
require __DIR__.'/admin.php';

// Load Seller Routes
require __DIR__.'/seller.php';

// Load Delivery Partner Routes
require __DIR__.'/delivery.php';

// Load POS Routes
require __DIR__.'/pos.php';

// Load Affiliate Routes
require __DIR__.'/affiliate.php';
