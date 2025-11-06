<?php

namespace App\Http\Middleware;

use App\Models\ClientSubscribe;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultTenantFallback
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantContext = app('tenant.context');
        
        if (!$tenantContext->getClientId()) {
            $defaultClient = ClientSubscribe::where('subdomain', 'demo')
                ->where('active', true)
                ->first();
            
            if ($defaultClient) {
                $tenantContext->setClient($defaultClient);
                $request->attributes->set('tenant', $defaultClient);
                $request->attributes->set('tenant_id', $defaultClient->id);
            }
        }
        
        return $next($request);
    }
}
