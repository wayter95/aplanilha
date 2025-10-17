<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenantContext = app('tenant.context');
        $clientId = $tenantContext->getClientId();
        
        if ($clientId && $model->getTable() !== 'client_subscribes') {
            $builder->where('client_id', $clientId);
        }
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenantScope', function (Builder $builder) {
            return $builder->withoutGlobalScope(TenantScope::class);
        });
        
        $builder->macro('withTenantScope', function (Builder $builder, $clientId) {
            return $builder->where('client_id', $clientId);
        });
    }
}
