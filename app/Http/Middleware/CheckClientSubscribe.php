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
        // Extract subdomain from host (e.g., bukjob.sistema -> bukjob)
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // Get first part as subdomain
        $subdomain = $parts[0];

        // Check for API requests with X-Tenant-Domain header (optional override)
        $headerSubdomain = $request->header('X-Tenant-Domain');
        if ($headerSubdomain) {
            // Extract only subdomain if full domain was provided
            if (strpos($headerSubdomain, '.') !== false) {
                $subdomain = explode('.', $headerSubdomain)[0];
            } else {
                $subdomain = $headerSubdomain;
            }
        }

        // If no subdomain is provided, return a 400 Bad Request response
        if (!$subdomain) {
            abort(400, 'Client subdomain is required');
        }

        // Attempt to find the client subscribe by subdomain
        $client = $this->clientService->findBySubdomain($subdomain);

        // If the client is not found, return a 404 Not Found response
        if (!$client) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Client not found'], 404);
            }
            abort(404, 'Client not found');
        }

        // Attach the full client instance to the request
        $request->merge(['client_subscribe' => $client]);

        // Store the client subscribe instance globally
        app()->instance('client_subscribe', $client);

        return $next($request);
    }
}
