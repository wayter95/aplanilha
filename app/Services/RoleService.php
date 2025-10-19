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

    /**
     * Get all roles with pagination and filters.
     */
    public function getAllRoles(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->roleRepository->getAllPaginated($perPage, $filters);
    }

    /**
     * Get all roles without pagination.
     */
    public function getAllRolesList(array $filters = []): Collection
    {
        return $this->roleRepository->getAll($filters);
    }

    /**
     * Get role by ID.
     */
    public function getRoleById(string $id): ?UserRole
    {
        return $this->roleRepository->findById($id);
    }

    /**
     * Create a new role.
     */
    public function createRole(array $data): UserRole
    {
        // Set client_id first
        if (!isset($data['client_id'])) {
            // Use default tenant for now
            $defaultTenant = \App\Models\ClientSubscribe::first();
            $clientId = $defaultTenant ? $defaultTenant->id : null;
            
            if ($clientId) {
                $data['client_id'] = $clientId;
            }
        }

        // Validate role name uniqueness (within the same client)
        if ($this->roleRepository->nameExists($data['name'], null, $data['client_id'] ?? null)) {
            throw new Exception('Nome da função já está em uso.');
        }

        // Set default values
        $data['is_active'] = $data['is_active'] ?? true;

        // Create role
        $role = $this->roleRepository->create($data);

        // Assign permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $this->roleRepository->assignPermissions($role->id, $data['permissions']);
        }

        return $role;
    }

    /**
     * Update an existing role.
     */
    public function updateRole(string $id, array $data): ?UserRole
    {
        // Validate role name uniqueness (excluding current role)
        if (isset($data['name']) && $this->roleRepository->nameExists($data['name'], $id)) {
            throw new Exception('Nome da função já está em uso.');
        }

        $role = $this->roleRepository->update($id, $data);

        // Update permissions if provided
        if ($role && isset($data['permissions']) && is_array($data['permissions'])) {
            $this->roleRepository->assignPermissions($role->id, $data['permissions']);
        }

        return $role;
    }

    /**
     * Delete a role.
     */
    public function deleteRole(string $id): bool
    {
        return $this->roleRepository->delete($id);
    }

    /**
     * Toggle role status (active/inactive).
     */
    public function toggleRoleStatus(string $id): ?UserRole
    {
        $role = $this->roleRepository->findById($id);

        if ($role) {
            $role->is_active = !$role->is_active;
            $role->save();
        }

        return $role;
    }

    /**
     * Get role statistics.
     */
    public function getRoleStatistics(): array
    {
        return [
            'totalRoles' => $this->roleRepository->countAll(),
            'activeRoles' => $this->roleRepository->countActive(),
            'inactiveRoles' => $this->roleRepository->countInactive(),
        ];
    }

    /**
     * Get available permissions.
     */
    public function getAvailablePermissions(): Collection
    {
        try {
            // Try to get tenant context if available
            $tenantContext = app('tenant.context');
            $clientId = $tenantContext->getClientId();
        } catch (\Exception $e) {
            // If tenant context is not available, use default tenant
            $defaultTenant = \App\Models\ClientSubscribe::first();
            $clientId = $defaultTenant ? $defaultTenant->id : null;
        }

        $query = \App\Models\UserPermission::query();
        
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        return $query->get();
    }

    /**
     * Validate role data.
     */
    public function validateRoleData(array $data, ?string $excludeId = null): array
    {
        $errors = [];

        // Get client_id for validation
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

        // Validate name uniqueness
        if ($this->roleRepository->nameExists($data['name'], $excludeId, $clientId)) {
            $errors['name'] = 'Este nome já está em uso.';
        }

        return $errors;
    }

    /**
     * Get roles by client ID.
     */
    public function getRolesByClientId(string $clientId): Collection
    {
        return $this->roleRepository->getByClientId($clientId);
    }

    /**
     * Assign permissions to role.
     */
    public function assignPermissionsToRole(string $roleId, array $permissionIds): bool
    {
        return $this->roleRepository->assignPermissions($roleId, $permissionIds);
    }

    /**
     * Remove permissions from role.
     */
    public function removePermissionsFromRole(string $roleId, array $permissionIds): bool
    {
        return $this->roleRepository->removePermissions($roleId, $permissionIds);
    }
}
