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

    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['permissions']);

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

    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['permissions'])
            ->where('client_id', $clientId);

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

    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->with(['permissions']);

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

    public function findById(string $id): ?UserRole
    {
        return $this->model->with(['permissions'])->find($id);
    }

    public function create(array $data): UserRole
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): ?UserRole
    {
        $role = $this->model->find($id);
        if ($role) {
            $role->update($data);
        }
        return $role;
    }

    public function delete(string $id): bool
    {
        return $this->model->destroy($id);
    }

    public function countAll(): int
    {
        return $this->model->count();
    }

    public function countActive(): int
    {
        return $this->model->where('is_active', true)->count();
    }

    public function countInactive(): int
    {
        return $this->model->where('is_active', false)->count();
    }

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

    public function getByClientId(string $clientId): Collection
    {
        return $this->model->where('client_id', $clientId)->get();
    }

    public function assignPermissions(string $roleId, array $permissionIds): bool
    {
        $role = $this->model->find($roleId);
        if ($role) {
            $role->permissions()->sync($permissionIds);
            return true;
        }
        return false;
    }

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
