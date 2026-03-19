<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate reviews from delivered orders daily at 2am
Schedule::command('reviews:generate')->dailyAt('02:00');

// Drip 1-3 reviews every hour at random intervals (skips ~40% of hours for natural pattern)
Schedule::command('reviews:drip-daily --min=1 --max=3')->hourly()->when(function () {
    // Skip random hours — only run ~60% of the time for unpredictable pattern
    return mt_rand(1, 100) <= 60;
});

// Publish scheduled social media posts every minute
Schedule::command('social:publish-scheduled')->everyMinute();

// Send abandoned cart reminders every 10 minutes
Schedule::command('cart:remind-abandoned')->everyTenMinutes();

// Refresh Instagram reels cache every 2 hours
Schedule::command('instagram:refresh-reels')->everyTwoHours();

// Refresh Instagram access token every 50 days (token lasts 60 days)
Schedule::command('instagram:refresh-token')->dailyAt('03:00')->when(function () {
    $lastRefresh = \App\Models\Setting::get('instagram_token_last_refresh');
    if (!$lastRefresh) return true;
    return now()->diffInDays(\Carbon\Carbon::parse($lastRefresh)) >= 50;
});
