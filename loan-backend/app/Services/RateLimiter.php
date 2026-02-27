<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RateLimiter
{
    /**
     * Check if a client/IP should be rate limited
     *
     * @param string $identifier Unique identifier (email, IP, user_id, etc.)
     * @param int $maxRequests Maximum requests allowed in the time window
     * @param int $windowSeconds Time window in seconds
     * @return bool True if rate limited, false if request is allowed
     */
    public static function isRateLimited(string $identifier, int $maxRequests = 5, int $windowSeconds = 3600): bool
    {
        $cacheKey = "rate_limit:{$identifier}";
        $countKey = "rate_limit_count:{$identifier}";

        // Get current count from cache
        $currentCount = Cache::get($countKey, 0);

        if ($currentCount >= $maxRequests) {
            return true;
        }

        return false;
    }

    /**
     * Record an API request for rate limiting purposes
     *
     * @param string $identifier Unique identifier
     * @param int $windowSeconds Time window in seconds
     */
    public static function recordRequest(string $identifier, int $windowSeconds = 3600): void
    {
        $countKey = "rate_limit_count:{$identifier}";
        $currentCount = Cache::get($countKey, 0);

        Cache::put($countKey, $currentCount + 1, $windowSeconds);
    }

    /**
     * Get remaining requests for a client
     *
     * @param string $identifier Unique identifier
     * @param int $maxRequests Maximum requests allowed
     * @return int Remaining requests
     */
    public static function getRemainingRequests(string $identifier, int $maxRequests = 5): int
    {
        $countKey = "rate_limit_count:{$identifier}";
        $currentCount = Cache::get($countKey, 0);

        return max(0, $maxRequests - $currentCount);
    }

    /**
     * Reset rate limit for a client
     *
     * @param string $identifier Unique identifier
     */
    public static function reset(string $identifier): void
    {
        $countKey = "rate_limit_count:{$identifier}";
        Cache::forget($countKey);
    }
}
