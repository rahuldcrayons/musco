<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Setting;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Apply timezone from settings
        try {
            $timezone = Setting::get('timezone', config('app.timezone'));
            if ($timezone && in_array($timezone, timezone_identifiers_list())) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }
        } catch (\Exception $e) {
            // Settings table may not exist during migrations
        }

        Route::model('address', UserAddress::class);

        Blade::directive('price', function (string $expression) {
            return "<?php echo format_price({$expression}); ?>";
        });

        // @setting('key', 'default') — pull value from DB settings
        Blade::directive('setting', function (string $expression) {
            return "<?php echo \App\Models\Setting::get({$expression}); ?>";
        });

        // @currency — output currency symbol (e.g. £)
        Blade::directive('currency', function () {
            return "<?php echo currency_symbol(); ?>";
        });

        View::composer('partials.mobile-nav', function ($view) {
            $view->with('navCategories', cache()->remember('nav.mobile.categories', 600, fn () =>
                Category::whereNull('parent_id')
                    ->where('is_active', true)
                    ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('position')])
                    ->orderBy('position')
                    ->get()
            ));
        });
    }
}
