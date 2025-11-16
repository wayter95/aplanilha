<?php

namespace App\Services;

use App\Models\ClientSubscribe;
use Illuminate\Support\Facades\Cache;

class ClientSubscribeService
{
    /**
     * Find a client subscribe by subdomain.
     *
     * @param string $subdomain
     * @return ClientSubscribe|null
     */
    public function findBySubdomain(string $subdomain): ?ClientSubscribe
    {
        // Cache the client lookup for 1 hour to improve performance
        return Cache::remember("client_subscribe:{$subdomain}", 3600, function () use ($subdomain) {
            return ClientSubscribe::where('subdomain', $subdomain)
                ->where('active', true) // Only active clients
                ->first();
        });
    }

    /**
     * Clear the cache for a specific subdomain.
     *
     * @param string $subdomain
     * @return void
     */
    public function clearCache(string $subdomain): void
    {
        Cache::forget("client_subscribe:{$subdomain}");
    }

    /**
     * Get the current client subscribe from the application instance.
     *
     * @return ClientSubscribe|null
     */
    public function getCurrentClient(): ?ClientSubscribe
    {
        return app('client_subscribe');
    }
}
