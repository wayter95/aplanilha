<?php

namespace App\Services\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{
    public function createUser(array $data): User;
    public function updateUser(string $id, array $data): User;
    public function deleteUser(string $id): bool;
    public function getUser(string $id): ?User;
    public function getUserByEmail(string $email, ?string $clientId = null): ?User;
    public function getUsersByClient(string $clientId): Collection;
    public function getMasterUsers(): Collection;
    public function authenticateUser(string $email, string $password, ?string $clientId = null): ?User;
    public function assignRoleToUser(string $userId, string $roleId): bool;
    public function removeRoleFromUser(string $userId, string $roleId): bool;
    public function userHasRole(string $userId, string $roleName): bool;
    public function userHasPermission(string $userId, string $module, string $action): bool;
    public function resetUserPassword(string $email, ?string $clientId = null): bool;
}
