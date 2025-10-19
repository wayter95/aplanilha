<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $clientId = $this->getCurrentClientId();
        
        if ($clientId) {
            $builder->where($model->getTable() . '.client_id', $clientId);
        }
    }

    protected function getCurrentClientId(): ?string
    {
        if (Auth::check() && Auth::user()->client_id) {
            return Auth::user()->client_id;
        }

        if (session()->has('client_id')) {
            return Session::get('client_id');
        }

        if (Request::hasHeader('X-Client-ID')) {
            return Request::header('X-Client-ID');
        }

        return null;
    }

    public function extend(Builder $builder): void
    {
        $this->addWithoutTenantScope($builder);
        $this->addWithTenantScope($builder);
    }

    protected function addWithoutTenantScope(Builder $builder): void
    {
        $builder->macro('withoutTenantScope', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }

    protected function addWithTenantScope(Builder $builder): void
    {
        $builder->macro('withTenantScope', function (Builder $builder, $clientId) {
            return $builder->where($builder->getModel()->getTable() . '.client_id', $clientId);
        });
    }
}