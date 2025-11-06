<?php

namespace App\Repositories;

use App\Models\UserRole;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(UserRole $model)
    {
        parent::__construct($model);
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

    public function find(string $id): ?Model
    {
        return $this->model->with(['permissions'])->find($id);
    }

    public function findById(string $id): ?UserRole
    {
        return $this->model->with(['permissions'])->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    // Método específico para compatibilidade com RoleRepositoryInterface
    // O create() acima retorna Model, mas a interface espera UserRole
    // Este método garante o tipo correto
    public function createRole(array $data): UserRole
    {
        /** @var UserRole $role */
        $role = $this->create($data);
        return $role;
    }

    public function update(string $id, array $data): bool
    {
        $role = $this->model->find($id);
        if (!$role) {
            return false;
        }
        return $role->update($data);
    }

    public function delete(string $id): bool
    {
        $role = $this->model->find($id);
        if (!$role) {
            return false;
        }
        return $role->delete();
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
