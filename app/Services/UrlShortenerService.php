<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Handles URL encoding and decoding via caching.
 */
class UrlShortenerService
{
    /**
     * Generates a short URL for the given original URL.
     */
    public function encode(string $originalUrl): string
    {
        $shorkey = Str::random(6);

        if (!Cache::has($shorkey)) {
            Cache::put($shorkey, $originalUrl, now()->addDays(30));
        }
        return URL::to($shorkey);
    }

    /**
     * Retrieves the original URL from a short URL.
     */
    public function decode(string $shortUrl): ?string
    {
        $shortKey = ltrim(parse_url($shortUrl, PHP_URL_PATH) ?? $shortUrl, '/');
        return Cache::get($shortKey);
    }
}
