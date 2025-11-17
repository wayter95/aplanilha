<?php

namespace App\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProjectTypeServiceInterface
{
    /**
     * Obter tipos de projeto por client_id com paginação
     */
    public function getProjectTypesByClient(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Obter todos os tipos de projeto por client_id sem paginação
     */
    public function getAllProjectTypesByClient(string $clientId): Collection;

    /**
     * Criar um novo tipo de projeto
     */
    public function create(array $data): object;

    /**
     * Atualizar tipo de projeto
     */
    public function update(string $id, array $data): bool;

    /**
     * Deletar tipo de projeto
     */
    public function delete(string $id): bool;

    /**
     * Buscar tipo de projeto por ID
     */
    public function findById(string $id): ?object;

    /**
     * Ativar tipo de projeto
     */
    public function activate(string $id): bool;

    /**
     * Bloquear tipo de projeto
     */
    public function block(string $id): bool;

    /**
     * Exportar tipos de projeto para CSV
     */
    public function exportToCsv(string $clientId, array $filters = []): string;
}
