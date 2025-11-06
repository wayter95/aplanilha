<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email, ?string $clientId = null): ?User;
    public function getByClient(string $clientId);
    public function getMasterUsers();
    public function getTenantUsers(string $clientId);
    public function assignRole(string $userId, string $roleId): bool;
    public function removeRole(string $userId, string $roleId): bool;
    public function hasRole(User $user, string $roleName): bool;
    public function hasPermission(User $user, string $module, string $action): bool;
}
