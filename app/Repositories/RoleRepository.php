<?php

namespace App\Repositories;

use App\Models\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository
{
    protected UserRole $model;

    public function __construct(UserRole $model)
    {
        $this->model = $model;
    }

    /**
     * Get all roles with pagination.
     */
    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['permissions']);

        // Apply filters
        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('display_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('is_active', $filters['status'] === 'Ativo');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all roles without pagination.
     */
    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->with(['permissions']);

        // Apply filters
        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('display_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('is_active', $filters['status'] === 'Ativo');
        }

        return $query->get();
    }

    /**
     * Find a role by ID.
     */
    public function findById(string $id): ?UserRole
    {
        return $this->model->with(['permissions'])->find($id);
    }

    /**
     * Create a new role.
     */
    public function create(array $data): UserRole
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing role.
     */
    public function update(string $id, array $data): ?UserRole
    {
        $role = $this->model->find($id);
        if ($role) {
            $role->update($data);
        }
        return $role;
    }

    /**
     * Delete a role.
     */
    public function delete(string $id): bool
    {
        return $this->model->destroy($id);
    }

    /**
     * Count all roles.
     */
    public function countAll(): int
    {
        return $this->model->count();
    }

    /**
     * Count active roles.
     */
    public function countActive(): int
    {
        return $this->model->where('is_active', true)->count();
    }

    /**
     * Count inactive roles.
     */
    public function countInactive(): int
    {
        return $this->model->where('is_active', false)->count();
    }

    /**
     * Check if role name exists.
     */
    public function nameExists(string $name, ?string $excludeId = null, ?string $clientId = null): bool
    {
        $query = $this->model->where('name', $name);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        if ($clientId) {
            $query->where('client_id', $clientId);
        }
        
        return $query->exists();
    }

    /**
     * Get roles by client ID.
     */
    public function getByClientId(string $clientId): Collection
    {
        return $this->model->where('client_id', $clientId)->get();
    }

    /**
     * Assign permissions to role.
     */
    public function assignPermissions(string $roleId, array $permissionIds): bool
    {
        $role = $this->model->find($roleId);
        if ($role) {
            $role->permissions()->sync($permissionIds);
            return true;
        }
        return false;
    }

    /**
     * Remove permissions from role.
     */
    public function removePermissions(string $roleId, array $permissionIds): bool
    {
        $role = $this->model->find($roleId);
        if ($role) {
            $role->permissions()->detach($permissionIds);
            return true;
        }
        return false;
    }
}
