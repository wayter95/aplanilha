<?php

namespace App\Services;

use App\Models\UserRole;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class RoleService
{
    protected RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAllRoles(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->roleRepository->getAllPaginated($perPage, $filters);
    }

    public function getAllRolesList(array $filters = []): Collection
    {
        return $this->roleRepository->getAll($filters);
    }

    public function getRoleById(string $id): ?UserRole
    {
        return $this->roleRepository->findById($id);
    }

    public function createRole(array $data): UserRole
    {
        if (!isset($data['client_id'])) {
            $defaultTenant = \App\Models\ClientSubscribe::first();
            $clientId = $defaultTenant ? $defaultTenant->id : null;
            
            if ($clientId) {
                $data['client_id'] = $clientId;
            }
        }

        if ($this->roleRepository->nameExists($data['name'], null, $data['client_id'] ?? null)) {
            throw new Exception('Nome da função já está em uso.');
        }

        $data['is_active'] = $data['is_active'] ?? true;

        $role = $this->roleRepository->create($data);

        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $this->roleRepository->assignPermissions($role->id, $data['permissions']);
        }

        return $role;
    }

    public function updateRole(string $id, array $data): ?UserRole
    {
        if (isset($data['name']) && $this->roleRepository->nameExists($data['name'], $id)) {
            throw new Exception('Nome da função já está em uso.');
        }

        $role = $this->roleRepository->update($id, $data);

        if ($role && isset($data['permissions']) && is_array($data['permissions'])) {
            $this->roleRepository->assignPermissions($role->id, $data['permissions']);
        }

        return $role;
    }

    public function deleteRole(string $id): bool
    {
        return $this->roleRepository->delete($id);
    }

    public function toggleRoleStatus(string $id): ?UserRole
    {
        $role = $this->roleRepository->findById($id);

        if ($role) {
            $role->is_active = !$role->is_active;
            $role->save();
        }

        return $role;
    }

    public function getRoleStatistics(): array
    {
        return [
            'totalRoles' => $this->roleRepository->countAll(),
            'activeRoles' => $this->roleRepository->countActive(),
            'inactiveRoles' => $this->roleRepository->countInactive(),
        ];
    }

    public function getAvailablePermissions(): Collection
    {
        try {
            $tenantContext = app('tenant.context');
            $clientId = $tenantContext->getClientId();
        } catch (\Exception $e) {
            $defaultTenant = \App\Models\ClientSubscribe::first();
            $clientId = $defaultTenant ? $defaultTenant->id : null;
        }

        $query = \App\Models\UserPermission::query();
        
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        return $query->get();
    }

    public function validateRoleData(array $data, ?string $excludeId = null): array
    {
        $errors = [];

        $clientId = $data['client_id'] ?? null;
        if (!$clientId) {
            try {
                $tenantContext = app('tenant.context');
                $clientId = $tenantContext->getClientId();
            } catch (\Exception $e) {
                $defaultTenant = \App\Models\ClientSubscribe::first();
                $clientId = $defaultTenant ? $defaultTenant->id : null;
            }
        }

        if ($this->roleRepository->nameExists($data['name'], $excludeId, $clientId)) {
            $errors['name'] = 'Este nome já está em uso.';
        }

        return $errors;
    }

    public function getRolesByClientId(string $clientId): Collection
    {
        return $this->roleRepository->getByClientId($clientId);
    }

    public function assignPermissionsToRole(string $roleId, array $permissionIds): bool
    {
        return $this->roleRepository->assignPermissions($roleId, $permissionIds);
    }

    
    public function removePermissionsFromRole(string $roleId, array $permissionIds): bool
    {
        return $this->roleRepository->removePermissions($roleId, $permissionIds);
    }
}
