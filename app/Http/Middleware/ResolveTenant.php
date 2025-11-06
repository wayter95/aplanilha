<?php

namespace App\Http\Middleware;

use App\Models\ClientSubscribe;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);
        
        if ($subdomain) {
            $client = ClientSubscribe::where('subdomain', $subdomain)
                ->where('active', true)
                ->first();
            
            if ($client) {
                app('tenant.context')->setClient($client);
                $request->attributes->set('tenant', $client);
                $request->attributes->set('tenant_id', $client->id);
            } else {
                return response()->json([
                    'error' => 'Tenant not found',
                    'subdomain' => $subdomain
                ], 404);
            }
        }
        
        return $next($request);
    }

    private function extractSubdomain(string $host): ?string
    {
        $parts = explode('.', $host);
        
        if (count($parts) >= 3) {
            $subdomain = $parts[0];
            return $subdomain !== 'www' ? $subdomain : null;
        }
        
        return null;
    }
}
