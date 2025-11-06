<?php

namespace App\Services\Interfaces;

use App\Models\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleServiceInterface
{
    public function getAllRoles(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function getRolesByClient(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function getAllRolesList(array $filters = []): Collection;
    public function getRoleById(string $id): ?UserRole;
    public function createRole(array $data): UserRole;
    public function updateRole(string $id, array $data): ?UserRole;
    public function deleteRole(string $id): bool;
    public function toggleRoleStatus(string $id): ?UserRole;
    public function getRoleStatistics(): array;
    public function getAvailablePermissions(): Collection;
    public function validateRoleData(array $data, ?string $excludeId = null): array;
    public function getRolesByClientId(string $clientId): Collection;
    public function assignPermissionsToRole(string $roleId, array $permissionIds): bool;
    public function removePermissionsFromRole(string $roleId, array $permissionIds): bool;
}


