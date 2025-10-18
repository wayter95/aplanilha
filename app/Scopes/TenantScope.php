<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get the current client_id from the authenticated user or session
        $clientId = $this->getCurrentClientId();
        
        if ($clientId) {
            $builder->where($model->getTable() . '.client_id', $clientId);
        }
    }

    /**
     * Get the current client ID from authentication or session.
     */
    protected function getCurrentClientId(): ?string
    {
        // Try to get from authenticated user first
        if (auth()->check() && auth()->user()->client_id) {
            return auth()->user()->client_id;
        }

        // Try to get from session
        if (session()->has('client_id')) {
            return session('client_id');
        }

        // Try to get from request header (for API calls)
        if (request()->hasHeader('X-Client-ID')) {
            return request()->header('X-Client-ID');
        }

        // Return null if no client context found
        return null;
    }

    /**
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder): void
    {
        $this->addWithoutTenantScope($builder);
        $this->addWithTenantScope($builder);
    }

    /**
     * Add the without-tenant-scope extension to the builder.
     */
    protected function addWithoutTenantScope(Builder $builder): void
    {
        $builder->macro('withoutTenantScope', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the with-tenant-scope extension to the builder.
     */
    protected function addWithTenantScope(Builder $builder): void
    {
        $builder->macro('withTenantScope', function (Builder $builder, $clientId) {
            return $builder->where($builder->getModel()->getTable() . '.client_id', $clientId);
        });
    }
}