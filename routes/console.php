<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate reviews from delivered orders daily at 2am
Schedule::command('reviews:generate')->dailyAt('02:00');

// Refresh Instagram reels cache every 2 hours
Schedule::command('instagram:refresh-reels')->everyTwoHours();

// Refresh Instagram access token every 50 days (token lasts 60 days)
Schedule::command('instagram:refresh-token')->dailyAt('03:00')->when(function () {
    $lastRefresh = \App\Models\Setting::get('instagram_token_last_refresh');
    if (!$lastRefresh) return true;
    return now()->diffInDays(\Carbon\Carbon::parse($lastRefresh)) >= 50;
});
