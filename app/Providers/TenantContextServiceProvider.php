<?php

namespace App\Providers;

use App\Models\ClientSubscribe;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;

class TenantContextServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('tenant.context', function () {
            return new class {
                private ?string $clientId = null;
                private ?ClientSubscribe $client = null;
                private ?string $subdomain = null;

                public function setClientId(?string $clientId): void
                {
                    $this->clientId = $clientId;
                    $this->client = null;
                }

                public function getClientId(): ?string
                {
                    return $this->clientId;
                }

                public function setClient(ClientSubscribe $client): void
                {
                    $this->client = $client;
                    $this->clientId = $client->id;
                }

                public function getClient(): ?ClientSubscribe
                {
                    if (!$this->client && $this->clientId) {
                        $this->client = ClientSubscribe::find($this->clientId);
                    }
                    return $this->client;
                }

                public function setSubdomain(?string $subdomain): void
                {
                    $this->subdomain = $subdomain;
                }

                public function getSubdomain(): ?string
                {
                    return $this->subdomain;
                }

                public function resolveFromRequest(): void
                {
                    $host = Request::getHost();
                    $subdomain = $this->extractSubdomain($host);
                    
                    $this->setSubdomain($subdomain);
                    
                    if ($subdomain) {
                        $client = ClientSubscribe::where('subdomain', $subdomain)
                            ->where('active', true)
                            ->first();
                        
                        if ($client) {
                            $this->setClient($client);
                        }
                    }
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

                public function clear(): void
                {
                    $this->clientId = null;
                    $this->client = null;
                    $this->subdomain = null;
                }
            };
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $this->app['tenant.context']->resolveFromRequest();
    }
}
