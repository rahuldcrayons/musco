<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware(['guest:admin', 'throttle:10,1'])->group(function () {
        Route::get('/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
    });

    // Authenticated admin routes
    Route::middleware(['auth:admin', 'admin', 'admin.audit'])->group(function () {
        Route::post('/logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/{notification}/read', [App\Http\Controllers\Admin\NotificationController::class, 'read'])->name('notifications.read');

        // AJAX search endpoints (used by multiple features)
        Route::get('/search/products', [App\Http\Controllers\Admin\SearchController::class, 'products'])->name('search.products');
        Route::get('/search/orders', [App\Http\Controllers\Admin\SearchController::class, 'orders'])->name('search.orders');
        Route::get('/search/customers', [App\Http\Controllers\Admin\SearchController::class, 'customers'])->name('search.customers');

        // Orders
        Route::middleware('admin.section:orders')->group(function () {
            // Abandoned Checkouts
            Route::get('/abandoned-checkouts', function (\Illuminate\Http\Request $request) {
                $tab = $request->get('tab', 'all');
                $query = \App\Models\AbandonedCheckout::latest();

                if ($tab === 'with-contact') {
                    $query->where(function ($q) { $q->whereNotNull('email')->orWhereNotNull('phone'); })->where('recovered', false);
                } elseif ($tab === 'recovered') {
                    $query->where('recovered', true);
                }

                $abandoned = $query->paginate(25)->appends(['tab' => $tab]);
                return view('admin.abandoned-checkouts', compact('abandoned'));
            })->name('abandoned-checkouts');

            Route::post('/abandoned-checkouts/bulk', function (\Illuminate\Http\Request $request) {
                $ids = $request->input('ids', []);
                $action = $request->input('action');

                if (empty($ids)) {
                    return back()->with('success', 'No items selected.');
                }

                if ($action === 'delete') {
                    $count = \App\Models\AbandonedCheckout::whereIn('id', $ids)->delete();
                    return back()->with('success', "{$count} abandoned checkouts deleted.");
                }

                if ($action === 'remind') {
                    $sent = 0;
                    $items = \App\Models\AbandonedCheckout::whereIn('id', $ids)->where('recovered', false)->get();
                    foreach ($items as $ac) {
                        if ($ac->email) {
                            try {
                                \Illuminate\Support\Facades\Mail::send('emails.abandoned-cart', ['ac' => $ac], function ($m) use ($ac) {
                                    $m->to($ac->email, $ac->name)->subject('You left something behind! Complete your order');
                                });
                                $sent++;
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Abandoned email failed: ' . $e->getMessage());
                            }
                        }
                        if ($ac->phone && class_exists(\App\Services\WhatsAppService::class)) {
                            try {
                                app(\App\Services\WhatsAppService::class)->sendAbandonedCartReminder($ac->phone, $ac->name ?: 'there', $ac->cart_total);
                                $sent++;
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('WhatsApp abandoned reminder failed: ' . $e->getMessage());
                            }
                        }
                        $ac->update(['notified_at' => now()]);
                    }
                    return back()->with('success', "Reminders sent to {$sent} contacts.");
                }

                return back();
            })->name('abandoned-checkouts.bulk');

            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
                Route::get('/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
                Route::put('/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('status');
                Route::post('/{order}/ship', [App\Http\Controllers\Admin\OrderController::class, 'ship'])->name('ship');
                Route::get('/{order}/invoice', [App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('invoice');
                Route::get('/{order}/packing-slip', [App\Http\Controllers\Admin\OrderController::class, 'packingSlip'])->name('packing-slip');
                Route::post('/{order}/assign-partner', [App\Http\Controllers\Admin\OrderController::class, 'assignPartner'])->name('assign-partner');
                Route::put('/{order}/expected-delivery', [App\Http\Controllers\Admin\OrderController::class, 'setExpectedDelivery'])->name('expected-delivery');
            });

            // Delivery (Delhivery Integration)
            Route::prefix('delivery')->name('delivery.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\DeliveryController::class, 'index'])->name('index');
                Route::post('/orders/{order}/book', [App\Http\Controllers\Admin\DeliveryController::class, 'book'])->name('book');
                Route::get('/orders/{order}/track', [App\Http\Controllers\Admin\DeliveryController::class, 'track'])->name('track');
                Route::post('/orders/{order}/cancel', [App\Http\Controllers\Admin\DeliveryController::class, 'cancel'])->name('cancel');
                Route::get('/orders/{order}/label', [App\Http\Controllers\Admin\DeliveryController::class, 'label'])->name('label');
                Route::post('/orders/{order}/ndr', [App\Http\Controllers\Admin\DeliveryController::class, 'ndrAction'])->name('ndr');
                Route::post('/pickup', [App\Http\Controllers\Admin\DeliveryController::class, 'requestPickup'])->name('pickup');
                Route::get('/check-pincode', [App\Http\Controllers\Admin\DeliveryController::class, 'checkPincode'])->name('check-pincode');
                Route::get('/calculate-cost', [App\Http\Controllers\Admin\DeliveryController::class, 'calculateCost'])->name('calculate-cost');
                Route::get('/warehouses', [App\Http\Controllers\Admin\DeliveryController::class, 'warehouses'])->name('warehouses');
            });

            // Returns
            Route::prefix('returns')->name('returns.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\ReturnController::class, 'index'])->name('index');
                Route::get('/{return}', [App\Http\Controllers\Admin\ReturnController::class, 'show'])->name('show');
                Route::put('/{return}/status', [App\Http\Controllers\Admin\ReturnController::class, 'updateStatus'])->name('status');
                Route::post('/{return}/refund', [App\Http\Controllers\Admin\ReturnController::class, 'processRefund'])->name('refund');
                Route::post('/{return}/assign-partner', [App\Http\Controllers\Admin\ReturnController::class, 'assignPartner'])->name('assign-partner');
            });

            // Credit Notes
            Route::prefix('credit-notes')->name('credit-notes.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\CreditNoteController::class, 'index'])->name('index');
                Route::get('/{creditNote}', [App\Http\Controllers\Admin\CreditNoteController::class, 'show'])->name('show');
            });
        });

        // Catalog
        Route::middleware('admin.section:catalog')->group(function () {
            // Products (export/import before resource to avoid route conflict)
            Route::get('/products/export', [App\Http\Controllers\Admin\ProductController::class, 'export'])->name('products.export');
            Route::post('/products/import', [App\Http\Controllers\Admin\ProductController::class, 'import'])->name('products.import');
            Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
            Route::put('/products/{product}/toggle-status', [App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
            Route::put('/products/{product}/toggle-featured', [App\Http\Controllers\Admin\ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
            Route::post('/products/{product}/duplicate', [App\Http\Controllers\Admin\ProductController::class, 'duplicate'])->name('products.duplicate');
            Route::post('/products/bulk-action', [App\Http\Controllers\Admin\ProductController::class, 'bulkAction'])->name('products.bulk-action');

            // Categories
            Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
            Route::put('/categories/{category}/toggle-status', [App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
            Route::post('/categories/reorder', [App\Http\Controllers\Admin\CategoryController::class, 'reorder'])->name('categories.reorder');

            // Brands
            Route::resource('brands', App\Http\Controllers\Admin\BrandController::class);

            // Attributes
            Route::resource('attributes', App\Http\Controllers\Admin\AttributeController::class);
            Route::resource('attributes.values', App\Http\Controllers\Admin\AttributeValueController::class)->shallow();

            // Inventory
            Route::prefix('inventory')->name('inventory.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('index');
                Route::get('/low-stock', [App\Http\Controllers\Admin\InventoryController::class, 'lowStock'])->name('low-stock');
                Route::get('/out-of-stock', [App\Http\Controllers\Admin\InventoryController::class, 'outOfStock'])->name('out-of-stock');
                Route::put('/{product_id}/stock', [App\Http\Controllers\Admin\InventoryController::class, 'updateStock'])->name('update-stock');
                Route::get('/movements', [App\Http\Controllers\Admin\InventoryController::class, 'movements'])->name('movements');
                Route::resource('locations', App\Http\Controllers\Admin\InventoryLocationController::class);
            });
        });

        // Customers
        Route::middleware('admin.section:customers')->group(function () {
            Route::resource('customers', App\Http\Controllers\Admin\CustomerController::class)->except(['create', 'store', 'destroy']);
            Route::put('/customers/{customer}/toggle-status', [App\Http\Controllers\Admin\CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
            Route::get('/customers/{customer}/orders', [App\Http\Controllers\Admin\CustomerController::class, 'orders'])->name('customers.orders');
        });

        // Sellers
        Route::middleware('admin.section:sellers')->group(function () {
            Route::prefix('sellers')->name('sellers.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\SellerController::class, 'index'])->name('index');
                Route::get('/pending', [App\Http\Controllers\Admin\SellerController::class, 'pending'])->name('pending');
                Route::get('/{seller}', [App\Http\Controllers\Admin\SellerController::class, 'show'])->name('show');
                Route::put('/{seller}', [App\Http\Controllers\Admin\SellerController::class, 'update'])->name('update');
                Route::post('/{seller}/approve', [App\Http\Controllers\Admin\SellerController::class, 'approve'])->name('approve');
                Route::post('/{seller}/reject', [App\Http\Controllers\Admin\SellerController::class, 'reject'])->name('reject');
                Route::post('/{seller}/suspend', [App\Http\Controllers\Admin\SellerController::class, 'suspend'])->name('suspend');
                Route::get('/{seller}/products', [App\Http\Controllers\Admin\SellerController::class, 'products'])->name('products');
                Route::get('/{seller}/payouts', [App\Http\Controllers\Admin\SellerController::class, 'payouts'])->name('payouts');
            });
        });

        // Staff (admin-only)
        Route::middleware('admin.section:staff')->group(function () {
            Route::resource('staff', App\Http\Controllers\Admin\StaffController::class);
        });

        // Delivery Partners
        Route::middleware('admin.section:delivery_partners')->group(function () {
            Route::resource('delivery-partners', App\Http\Controllers\Admin\DeliveryPartnerController::class);
            Route::put('/delivery-partners/{deliveryPartner}/toggle-status', [App\Http\Controllers\Admin\DeliveryPartnerController::class, 'toggleStatus'])->name('delivery-partners.toggle-status');
        });

        // Marketing
        Route::middleware('admin.section:marketing')->group(function () {
            // Marketing Hub & Meta OAuth
            Route::prefix('marketing')->name('marketing.')->group(function () {
                Route::get('/hub', [App\Http\Controllers\Admin\MetaConnectController::class, 'index'])->name('hub');
                Route::get('/meta/redirect', [App\Http\Controllers\Admin\MetaConnectController::class, 'redirect'])->name('meta.redirect');
                Route::get('/meta/callback', [App\Http\Controllers\Admin\MetaConnectController::class, 'callback'])->name('meta.callback');
                Route::delete('/meta/disconnect', [App\Http\Controllers\Admin\MetaConnectController::class, 'disconnect'])->name('meta.disconnect');
            });

            Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class);
            Route::resource('flash-sales', App\Http\Controllers\Admin\FlashSaleController::class);
            Route::resource('banners', App\Http\Controllers\Admin\BannerController::class);
            Route::post('/banners/reorder', [App\Http\Controllers\Admin\BannerController::class, 'reorder'])->name('banners.reorder');

            // Affiliates
            Route::prefix('affiliates')->name('affiliates.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\AffiliateController::class, 'index'])->name('index');
                Route::get('/redemptions', [App\Http\Controllers\Admin\AffiliateController::class, 'redemptions'])->name('redemptions');
                Route::get('/{affiliate}', [App\Http\Controllers\Admin\AffiliateController::class, 'show'])->name('show');
                Route::post('/{affiliate}/approve', [App\Http\Controllers\Admin\AffiliateController::class, 'approve'])->name('approve');
                Route::post('/{affiliate}/reject', [App\Http\Controllers\Admin\AffiliateController::class, 'reject'])->name('reject');
                Route::post('/{affiliate}/suspend', [App\Http\Controllers\Admin\AffiliateController::class, 'suspend'])->name('suspend');
                Route::post('/redemptions/{redemption}/process', [App\Http\Controllers\Admin\AffiliateController::class, 'processRedemption'])->name('redemptions.process');
                Route::post('/redemptions/{redemption}/complete', [App\Http\Controllers\Admin\AffiliateController::class, 'completeRedemption'])->name('redemptions.complete');
                Route::post('/redemptions/{redemption}/fail', [App\Http\Controllers\Admin\AffiliateController::class, 'failRedemption'])->name('redemptions.fail');
            });

            // Social Media Content Calendar
            Route::prefix('social-calendar')->name('social-calendar.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\SocialMediaPostController::class, 'index'])->name('index');
                Route::get('/calendar-data', [App\Http\Controllers\Admin\SocialMediaPostController::class, 'calendarData'])->name('calendar-data');
                Route::get('/create', [App\Http\Controllers\Admin\SocialMediaPostController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Admin\SocialMediaPostController::class, 'store'])->name('store');
                Route::get('/{post}/edit', [App\Http\Controllers\Admin\SocialMediaPostController::class, 'edit'])->name('edit');
                Route::put('/{post}', [App\Http\Controllers\Admin\SocialMediaPostController::class, 'update'])->name('update');
                Route::delete('/{post}', [App\Http\Controllers\Admin\SocialMediaPostController::class, 'destroy'])->name('destroy');
                Route::post('/{post}/publish-now', [App\Http\Controllers\Admin\SocialMediaPostController::class, 'publishNow'])->name('publish-now');
                Route::post('/{post}/retry', [App\Http\Controllers\Admin\SocialMediaPostController::class, 'retry'])->name('retry');
            });

            // Newsletter
            Route::prefix('newsletter')->name('newsletter.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('index');
                Route::delete('/{newsletter}', [App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('destroy');
                Route::put('/{newsletter}/toggle-status', [App\Http\Controllers\Admin\NewsletterController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('/bulk-action', [App\Http\Controllers\Admin\NewsletterController::class, 'bulkAction'])->name('bulk-action');
                Route::get('/export', [App\Http\Controllers\Admin\NewsletterController::class, 'export'])->name('export');
            });
        });

        // Content
        Route::middleware('admin.section:content')->group(function () {
            Route::resource('pages', App\Http\Controllers\Admin\PageController::class);
            Route::resource('blog-posts', App\Http\Controllers\Admin\BlogPostController::class);

            Route::prefix('reviews')->name('reviews.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('index');
                Route::get('/pending', [App\Http\Controllers\Admin\ReviewController::class, 'pending'])->name('pending');
                Route::get('/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('show');
                Route::put('/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'update'])->name('update');
                Route::post('/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('approve');
                Route::post('/{review}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reject');
                Route::delete('/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('destroy');
            });
        });

        // Support Tickets
        Route::prefix('support-tickets')->name('support-tickets.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('index');
            Route::get('/{supportTicket}', [App\Http\Controllers\Admin\SupportTicketController::class, 'show'])->name('show');
            Route::post('/{supportTicket}/reply', [App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('reply');
            Route::put('/{supportTicket}/status', [App\Http\Controllers\Admin\SupportTicketController::class, 'updateStatus'])->name('status');
            Route::delete('/{supportTicket}', [App\Http\Controllers\Admin\SupportTicketController::class, 'destroy'])->name('destroy');
        });

        // Enquiries
        Route::prefix('enquiries')->name('enquiries.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\EnquiryController::class, 'index'])->name('index');
            Route::get('/{enquiry}', [App\Http\Controllers\Admin\EnquiryController::class, 'show'])->name('show');
            Route::put('/{enquiry}/toggle-read', [App\Http\Controllers\Admin\EnquiryController::class, 'toggleRead'])->name('toggle-read');
            Route::put('/{enquiry}/status', [App\Http\Controllers\Admin\EnquiryController::class, 'updateStatus'])->name('status');
            Route::delete('/{enquiry}', [App\Http\Controllers\Admin\EnquiryController::class, 'destroy'])->name('destroy');
        });

        // Reports
        Route::middleware('admin.section:reports')->group(function () {
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/sales', [App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('sales');
                Route::get('/analytics', [App\Http\Controllers\Admin\ReportController::class, 'analytics'])->name('analytics');
                Route::get('/products', [App\Http\Controllers\Admin\ReportController::class, 'products'])->name('products');
                Route::get('/customers', [App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('customers');
                Route::get('/sellers', [App\Http\Controllers\Admin\ReportController::class, 'sellers'])->name('sellers');
                Route::get('/inventory', [App\Http\Controllers\Admin\InventoryReportController::class, 'index'])->name('inventory');
                Route::get('/export/{type}', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
                Route::get('/export-excel/{type}', [App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('export-excel');
            });
        });

        // Fraud Review
        Route::prefix('fraud')->name('fraud.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\FraudController::class, 'index'])->name('index');
            Route::get('/{fraudLog}', [App\Http\Controllers\Admin\FraudController::class, 'show'])->name('show');
            Route::put('/{fraudLog}/review', [App\Http\Controllers\Admin\FraudController::class, 'review'])->name('review');
        });

        // Settings (admin-only)
        Route::middleware('admin.section:settings')->group(function () {
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/general', [App\Http\Controllers\Admin\SettingController::class, 'general'])->name('general');
                Route::put('/general', [App\Http\Controllers\Admin\SettingController::class, 'updateGeneral'])->name('general.update');

                Route::get('/payment', [App\Http\Controllers\Admin\SettingController::class, 'payment'])->name('payment');
                Route::put('/payment', [App\Http\Controllers\Admin\SettingController::class, 'updatePayment'])->name('payment.update');

                Route::get('/shipping', [App\Http\Controllers\Admin\SettingController::class, 'shipping'])->name('shipping');
                Route::put('/shipping', [App\Http\Controllers\Admin\SettingController::class, 'updateShipping'])->name('shipping.update');

                Route::get('/tax', [App\Http\Controllers\Admin\SettingController::class, 'tax'])->name('tax');
                Route::put('/tax', [App\Http\Controllers\Admin\SettingController::class, 'updateTax'])->name('tax.update');

                Route::get('/email', [App\Http\Controllers\Admin\SettingController::class, 'email'])->name('email');
                Route::put('/email', [App\Http\Controllers\Admin\SettingController::class, 'updateEmail'])->name('email.update');

                Route::get('/seo', [App\Http\Controllers\Admin\SettingController::class, 'seo'])->name('seo');
                Route::put('/seo', [App\Http\Controllers\Admin\SettingController::class, 'updateSeo'])->name('seo.update');

                Route::get('/product-card', [App\Http\Controllers\Admin\SettingController::class, 'productCard'])->name('product-card');
                Route::put('/product-card', [App\Http\Controllers\Admin\SettingController::class, 'updateProductCard'])->name('product-card.update');

                Route::post('/import', [App\Http\Controllers\Admin\SettingController::class, 'import'])->name('import');
                Route::get('/import/template', [App\Http\Controllers\Admin\SettingController::class, 'importTemplate'])->name('import.template');
                Route::post('/email/test', [App\Http\Controllers\Admin\SettingController::class, 'testEmail'])->name('email.test');

                // Tax Rates
                Route::resource('tax-rates', App\Http\Controllers\Admin\TaxRateController::class);

                // Shipping Zones
                Route::resource('shipping-zones', App\Http\Controllers\Admin\ShippingZoneController::class);
                Route::resource('shipping-zones.rates', App\Http\Controllers\Admin\ShippingRateController::class)->shallow();

                // Currencies
                Route::resource('currencies', App\Http\Controllers\Admin\CurrencyController::class);

                // Roles & Permissions
                Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
            });

            // Stores (POS)
            Route::resource('stores', App\Http\Controllers\Admin\StoreController::class);
        });

        // Storefront / Homepage Manager
        Route::middleware('admin.section:storefront')->group(function () {
            Route::prefix('homepage')->name('homepage.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\HomepageController::class, 'index'])->name('index');

                // Site Settings
                Route::get('/site-settings', [App\Http\Controllers\Admin\HomepageController::class, 'siteSettings'])->name('site-settings');
                Route::put('/site-settings', [App\Http\Controllers\Admin\HomepageController::class, 'updateSiteSettings'])->name('site-settings.update');

                // Hero Banners
                Route::get('/hero-banners', [App\Http\Controllers\Admin\HomepageController::class, 'heroBanners'])->name('hero-banners');
                Route::post('/hero-banners', [App\Http\Controllers\Admin\HomepageController::class, 'storeHeroBanner'])->name('hero-banners.store');
                Route::put('/hero-banners/{banner}', [App\Http\Controllers\Admin\HomepageController::class, 'updateHeroBanner'])->name('hero-banners.update');
                Route::put('/hero-banners/{banner}/toggle', [App\Http\Controllers\Admin\HomepageController::class, 'toggleHeroBanner'])->name('hero-banners.toggle');
                Route::delete('/hero-banners/{banner}', [App\Http\Controllers\Admin\HomepageController::class, 'deleteHeroBanner'])->name('hero-banners.destroy');
                Route::post('/hero-banners/reorder', [App\Http\Controllers\Admin\HomepageController::class, 'reorderHeroBanners'])->name('hero-banners.reorder');

                // Sections
                Route::get('/sections', [App\Http\Controllers\Admin\HomepageController::class, 'sections'])->name('sections');
                Route::get('/sections/{section}', [App\Http\Controllers\Admin\HomepageController::class, 'editSection'])->name('sections.edit');
                Route::put('/sections/{section}', [App\Http\Controllers\Admin\HomepageController::class, 'updateSection'])->name('sections.update');
                Route::put('/sections/{section}/toggle', [App\Http\Controllers\Admin\HomepageController::class, 'toggleSection'])->name('sections.toggle');
                Route::post('/sections/reorder', [App\Http\Controllers\Admin\HomepageController::class, 'reorderSections'])->name('sections.reorder');

                // Testimonials
                Route::get('/testimonials', [App\Http\Controllers\Admin\HomepageController::class, 'testimonials'])->name('testimonials');
                Route::post('/testimonials', [App\Http\Controllers\Admin\HomepageController::class, 'storeTestimonial'])->name('testimonials.store');
                Route::put('/testimonials/{testimonial}', [App\Http\Controllers\Admin\HomepageController::class, 'updateTestimonial'])->name('testimonials.update');
                Route::put('/testimonials/{testimonial}/toggle', [App\Http\Controllers\Admin\HomepageController::class, 'toggleTestimonial'])->name('testimonials.toggle');
                Route::delete('/testimonials/{testimonial}', [App\Http\Controllers\Admin\HomepageController::class, 'deleteTestimonial'])->name('testimonials.destroy');

                // About Page Settings
                Route::get('/about-settings', [App\Http\Controllers\Admin\HomepageController::class, 'aboutSettings'])->name('about-settings');
                Route::put('/about-settings', [App\Http\Controllers\Admin\HomepageController::class, 'updateAboutSettings'])->name('about-settings.update');

                // Navigation
                Route::get('/navigation', [App\Http\Controllers\Admin\HomepageController::class, 'navigation'])->name('navigation');
                Route::post('/navigation', [App\Http\Controllers\Admin\HomepageController::class, 'storeNavItem'])->name('navigation.store');
                Route::put('/navigation/{menu}', [App\Http\Controllers\Admin\HomepageController::class, 'updateNavItem'])->name('navigation.update');
                Route::delete('/navigation/{menu}', [App\Http\Controllers\Admin\HomepageController::class, 'deleteNavItem'])->name('navigation.destroy');
            });
        });
    });
});
