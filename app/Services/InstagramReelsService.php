<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramReelsService
{
    private string $accessToken;
    private string $igUserId;
    private string $apiVersion = 'v21.0';

    public function __construct()
    {
        // DB token takes priority (auto-refreshed), fallback to .env
        $this->accessToken = Setting::get('instagram_access_token') ?: (config('services.instagram.access_token') ?? '');
        $this->igUserId = config('services.instagram.user_id') ?? '';
    }

    /**
     * Get latest reels from Instagram, cached for 1 hour.
     */
    public function getLatestReels(int $limit = 10): array
    {
        if (empty($this->accessToken) || empty($this->igUserId)) {
            return [];
        }

        return Cache::remember('instagram_reels', 3600, function () use ($limit) {
            return $this->fetchReelsFromApi($limit);
        });
    }

    /**
     * Fetch reels from Instagram Graph API.
     */
    private function fetchReelsFromApi(int $limit): array
    {
        try {
            // Fetch media from Instagram Graph API — filter for REELS/VIDEO
            $response = Http::timeout(15)->get(
                "https://graph.instagram.com/{$this->apiVersion}/{$this->igUserId}/media",
                [
                    'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp',
                    'limit' => 50, // Fetch more to filter reels
                    'access_token' => $this->accessToken,
                ]
            );

            if ($response->failed()) {
                Log::warning('Instagram API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json('data', []);

            // Filter only reels (VIDEO type) and take the required count
            $reels = collect($data)
                ->filter(fn($item) => in_array($item['media_type'], ['VIDEO', 'REELS']))
                ->take($limit)
                ->map(fn($item) => [
                    'id' => $item['id'],
                    'shortcode' => $this->extractShortcode($item['permalink'] ?? ''),
                    'permalink' => $item['permalink'] ?? '',
                    'caption' => $item['caption'] ?? '',
                    'thumbnail_url' => $item['thumbnail_url'] ?? '',
                    'media_url' => $item['media_url'] ?? '',
                    'timestamp' => $item['timestamp'] ?? '',
                ])
                ->values()
                ->toArray();

            return $reels;
        } catch (\Exception $e) {
            Log::error('Instagram Reels fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Extract shortcode from Instagram permalink.
     * e.g. https://www.instagram.com/reel/ABC123/ → ABC123
     */
    private function extractShortcode(string $permalink): string
    {
        if (preg_match('#/(?:reel|p)/([A-Za-z0-9_-]+)#', $permalink, $matches)) {
            return $matches[1];
        }
        return '';
    }

    /**
     * Clear the cached reels.
     */
    public function clearCache(): void
    {
        Cache::forget('instagram_reels');
    }
}
