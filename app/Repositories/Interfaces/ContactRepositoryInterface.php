<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Obter todos os contatos por client_id com paginação
     */
    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Obter todos os contatos por client_id sem paginação
     */
    public function getAllByClient(string $clientId, array $filters = []): Collection;

    /**
     * Obter contatos por tipo
     */
    public function getByType(string $clientId, string $type): Collection;

    /**
     * Obter contatos do tipo customer
     */
    public function getCustomers(string $clientId): Collection;

    /**
     * Obter contatos do tipo supplier
     */
    public function getSuppliers(string $clientId): Collection;

    /**
     * Obter contatos do tipo location
     */
    public function getLocations(string $clientId): Collection;

    /**
     * Buscar contatos para Select2 (com busca)
     */
    public function searchForSelect2(string $clientId, string $search = '', string $type = null, int $limit = 10): array;
}
