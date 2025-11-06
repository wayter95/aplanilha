<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantResolved
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantContext = app('tenant.context');
        
        if (!$tenantContext->getClientId()) {
            return response()->json([
                'error' => 'Tenant context not resolved',
                'message' => 'This route requires a valid tenant context'
            ], 400);
        }
        
        return $next($request);
    }
}
