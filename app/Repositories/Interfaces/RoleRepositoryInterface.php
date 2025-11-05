<?php

namespace App\Repositories\Interfaces;

use App\Models\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function getAll(array $filters = []): Collection;
    public function findById(string $id): ?UserRole;
    // create() é herdado de BaseRepositoryInterface e retorna Model
    // Para compatibilidade com código que espera UserRole, use createRole() ou faça cast
    public function delete(string $id): bool;
    public function countAll(): int;
    public function countActive(): int;
    public function countInactive(): int;
    public function nameExists(string $name, ?string $excludeId = null, ?string $clientId = null): bool;
    public function getByClientId(string $clientId): Collection;
    public function assignPermissions(string $roleId, array $permissionIds): bool;
    public function removePermissions(string $roleId, array $permissionIds): bool;
}


