<?php

namespace App\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContactServiceInterface
{
    /**
     * Obter contatos por client_id com paginação
     */
    public function getContactsByClient(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Obter todos os contatos por client_id sem paginação
     */
    public function getAllContactsByClient(string $clientId): Collection;

    /**
     * Criar um novo contato
     */
    public function create(array $data): object;

    /**
     * Atualizar contato
     */
    public function update(string $id, array $data): bool;

    /**
     * Deletar contato
     */
    public function delete(string $id): bool;

    /**
     * Buscar contato por ID
     */
    public function findById(string $id): ?object;

    /**
     * Obter contatos por tipo
     */
    public function getByType(string $clientId, string $type): Collection;

    /**
     * Obter clientes (tipo customer)
     */
    public function getCustomers(string $clientId): Collection;

    /**
     * Obter fornecedores (tipo supplier)
     */
    public function getSuppliers(string $clientId): Collection;

    /**
     * Obter locais (tipo location)
     */
    public function getLocations(string $clientId): Collection;

    /**
     * Buscar contatos para Select2
     */
    public function searchForSelect2(string $clientId, string $search = '', string $type = null, int $limit = 10): array;
}
