<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Aplica o scope de tenant nas queries
     * 
     * Lógica:
     * - Se o usuário logado é master (is_master = true), não aplica filtro (pode ver todos)
     * - Se há client_id no contexto, aplica filtro por client_id
     * - Se não há contexto mas usuário logado, usa client_id do usuário
     */
    public function apply(Builder $builder, Model $model): void
    {
        // ⚠️ CRÍTICO: Para modelo User, NUNCA chama Auth::user() aqui!
        // Isso causaria loop infinito: Auth::user() → Query User → TenantScope → Auth::user()
        
        // ⚠️ PROTEÇÃO ESPECIAL PARA MODELO USER
        // Para evitar loop infinito, o TenantScope não deve aplicar filtro
        // quando estamos buscando o próprio usuário autenticado via Auth::user()
        if ($model instanceof \App\Models\User) {
            // Para User, não aplica filtro por enquanto
            // O filtro será aplicado manualmente nas queries específicas quando necessário
            // Isso evita o loop: Auth::user() → TenantScope → Auth::user()
            return;
        }
        
        // Para outros modelos, aplica a lógica normal
        if (!Auth::check()) {
            // Sem usuário logado, não aplica filtro
            return;
        }
        
        // Obtém client_id sem chamar Auth::user() (para evitar loops)
        $clientId = $this->getCurrentClientIdWithoutUser();
        
        if ($clientId) {
            // Aplica filtro por client_id, incluindo registros globais
            $builder->where(function ($query) use ($clientId, $model) {
                $query->where($model->getTable() . '.client_id', $clientId)
                      ->orWhereNull($model->getTable() . '.client_id');
            });
        }
        // Se não há client_id, não aplica filtro (permite páginas públicas funcionarem)
    }

    /**
     * Obtém o client_id atual do contexto SEM chamar Auth::user()
     * Isso evita loops infinitos quando o TenantScope está aplicado ao modelo User
     */
    protected function getCurrentClientIdWithoutUser(): ?string
    {
        // Tenta obter do tenant context (melhor forma)
        try {
            $tenantContext = app('tenant.context');
            if ($tenantContext && method_exists($tenantContext, 'getClientId')) {
                $clientId = $tenantContext->getClientId();
                if ($clientId) {
                    return $clientId;
                }
            }
        } catch (\Exception $e) {
            // Tenant context não disponível, continua para outros métodos
        }

        // Fallback: session (NÃO chama Auth::user() para evitar loop)
        if (session()->has('client_id')) {
            return session('client_id');
        }

        // Fallback: header HTTP
        if (request()->hasHeader('X-Client-ID')) {
            return request()->header('X-Client-ID');
        }

        return null;
    }

    /**
     * Obtém o client_id atual do contexto (versão com Auth::user())
     * ⚠️ NÃO USE este método dentro do apply() quando o modelo é User!
     */
    protected function getCurrentClientId(): ?string
    {
        // Primeiro tenta sem Auth::user()
        $clientId = $this->getCurrentClientIdWithoutUser();
        if ($clientId) {
            return $clientId;
        }

        // Fallback: usuário logado (só use quando NÃO estiver processando User model)
        $user = Auth::user();
        if ($user && isset($user->client_id) && $user->client_id) {
            return $user->client_id;
        }

        return null;
    }

    /**
     * Estende o builder com métodos auxiliares
     */
    public function extend(Builder $builder): void
    {
        $this->addWithoutTenantScope($builder);
        $this->addWithTenantScope($builder);
    }

    /**
     * Adiciona método para remover o scope
     */
    protected function addWithoutTenantScope(Builder $builder): void
    {
        $builder->macro('withoutTenantScope', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Adiciona método para aplicar scope manualmente
     */
    protected function addWithTenantScope(Builder $builder): void
    {
        $builder->macro('withTenantScope', function (Builder $builder, $clientId) {
            return $builder->where(function ($query) use ($clientId, $builder) {
                $query->where($builder->getModel()->getTable() . '.client_id', $clientId)
                      ->orWhereNull($builder->getModel()->getTable() . '.client_id');
            });
        });
    }
}
