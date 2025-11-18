<?php

namespace App\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProjectServiceInterface
{
    /**
     * Obter projetos por client_id com paginação
     */
    public function getProjectsByClient(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Obter todos os projetos por client_id sem paginação
     */
    public function getAllProjectsByClient(string $clientId): Collection;

    /**
     * Criar um novo projeto
     */
    public function create(array $data): object;

    /**
     * Atualizar projeto
     */
    public function update(string $id, array $data): bool;

    /**
     * Deletar projeto
     */
    public function delete(string $id): bool;

    /**
     * Buscar projeto por ID
     */
    public function findById(string $id): ?object;

    /**
     * Ativar projeto (status = active)
     */
    public function activate(string $id): bool;

    /**
     * Cancelar projeto (status = cancelled)
     */
    public function cancel(string $id): bool;

    /**
     * Completar projeto (status = completed)
     */
    public function complete(string $id): bool;

    /**
     * Obter projetos por tipo
     */
    public function getByProjectType(string $projectTypeId): Collection;

    /**
     * Obter projetos por status
     */
    public function getByStatus(string $clientId, string $status): Collection;
}
