<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ClientSubscribeService;

class CheckClientSubscribe
{
    protected $clientService;

    /**
     * Constructor to inject the client subscribe service.
     *
     * @param ClientSubscribeService $clientService
     */
    public function __construct(ClientSubscribeService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * Handle an incoming request and check for a client subscribe.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the subdomain from the request header (sent by Axios)
        $subdomain = $request->header('X-Tenant-Domain') ?: $request->header('domain');

        // If no subdomain in header, try to extract from host
        if (!$subdomain) {
            $host = $request->getHost();
            $parts = explode('.', $host);
            
            // If has subdomain (e.g., client.example.com)
            if (count($parts) > 2) {
                $subdomain = $parts[0];
            }
        }

        // Extract only subdomain if full domain was provided
        if ($subdomain && strpos($subdomain, '.') !== false) {
            $subdomain = explode('.', $subdomain)[0];
        }

        // If no subdomain is provided, return a 400 Bad Request response
        if (!$subdomain) {
            return response()->json(['error' => 'Subdomain not provided'], 400);
        }

        // Attempt to find the client subscribe by subdomain
        $client = $this->clientService->findBySubdomain($subdomain);

        // If the client is not found, return a 404 Not Found response
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        // Attach the full client instance to the request
        $request->merge(['client_subscribe' => $client]);

        // Store the client subscribe instance globally
        app()->instance('client_subscribe', $client);

        return $next($request);
    }
}
