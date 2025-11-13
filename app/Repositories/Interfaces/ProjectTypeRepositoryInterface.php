<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProjectTypeRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Obter todos os tipos de projeto por client_id com paginação
     */
    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Obter todos os tipos de projeto por client_id sem paginação
     */
    public function getAllByClient(string $clientId, array $filters = []): Collection;

    /**
     * Verificar se título já existe para o cliente
     */
    public function titleExistsForClient(string $title, string $clientId, ?string $excludeId = null): bool;

    /**
     * Obter tipos de projeto ativos
     */
    public function getActiveTypes(string $clientId): Collection;
}
