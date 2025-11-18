<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Obter todos os projetos por client_id com paginação
     */
    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Obter todos os projetos por client_id sem paginação
     */
    public function getAllByClient(string $clientId, array $filters = []): Collection;

    /**
     * Verificar se número de projeto já existe para o cliente
     */
    public function projectNumberExistsForClient(string $projectNumber, string $clientId, ?string $excludeId = null): bool;

    /**
     * Obter projetos ativos
     */
    public function getActiveProjects(string $clientId): Collection;

    /**
     * Obter projetos por tipo
     */
    public function getByProjectType(string $projectTypeId): Collection;

    /**
     * Obter projetos por status
     */
    public function getByStatus(string $clientId, string $status): Collection;
}
