<?php

namespace App\Services\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    // Métodos da interface original (para compatibilidade com API)
    public function createUser(array $data): User;
    public function updateUser(string $id, array $data): User;
    public function deleteUser(string $id): bool;
    public function getUser(string $id): ?User;
    public function getUserByEmail(string $email, ?string $clientId = null): ?User;
    public function getUsersByClient(string $clientId): Collection;
    public function getUsersByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function getMasterUsers(): Collection;
    public function authenticateUser(string $email, string $password, ?string $clientId = null): ?User;
    public function assignRoleToUser(string $userId, string $roleId): bool;
    public function removeRoleFromUser(string $userId, string $roleId): bool;
    public function userHasRole(string $userId, string $roleName): bool;
    public function userHasPermission(string $userId, string $module, string $action): bool;
    public function resetUserPassword(string $email, ?string $clientId = null): bool;
    
    // Métodos adicionais do UserService (Web)
    public function getAllUsers(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function getAllUsersList(array $filters = []): Collection;
    public function getUserById(string $id): ?User;
    public function getUsersByRole(string $roleName): Collection;
    public function getActiveUsers(): Collection;
    public function getInactiveUsers(): Collection;
    public function getUserStatistics(): array;
    public function toggleUserStatus(string $id): User;
    public function getUsersWithPermissions(): Collection;
    public function assignRole(string $userId, string $roleId): bool;
    public function removeRole(string $userId, string $roleId): bool;
    public function getAvailableRoles(): Collection;
    public function validateUserData(array $data, ?string $excludeId = null): array;
}
