<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserRole;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['roles', 'client']);

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('is_active', $filters['status'] === 'Ativo');
        }

        if (isset($filters['role']) && $filters['role']) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        return $query->paginate($perPage);
    }

    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['roles', 'client'])
            ->where('client_id', $clientId);

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('is_active', $filters['status'] === 'Ativo');
        }

        if (isset($filters['role']) && $filters['role']) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        return $query->paginate($perPage);
    }

    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->with(['roles', 'client']);

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('is_active', $filters['status'] === 'Ativo');
        }

        if (isset($filters['role']) && $filters['role']) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        return $query->get();
    }

    public function findById(string $id): ?User
    {
        return $this->model->with(['roles', 'client'])->find($id);
    }

    public function findByEmail(string $email, ?string $clientId = null): ?User
    {
        $query = $this->model->where('email', $email);
        
        if ($clientId) {
            $query->where('client_id', $clientId);
        }
        
        return $query->first();
    }
    
    public function getByClient(string $clientId): Collection
    {
        return $this->model->where('client_id', $clientId)->get();
    }
    
    public function getMasterUsers(): Collection
    {
        return $this->model->where('is_master', true)->get();
    }
    
    public function getTenantUsers(string $clientId): Collection
    {
        return $this->model->where('client_id', $clientId)->where('is_master', false)->get();
    }
    
    public function hasRole(User $user, string $roleName): bool
    {
        return $user->roles()->where('name', $roleName)->exists();
    }
    
    public function hasPermission(User $user, string $module, string $action): bool
    {
        return $user->hasPermission($module, $action);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    public function delete(string $id): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    public function getByRole(string $roleName): Collection
    {
        return $this->model->whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })->get();
    }

    public function getActiveUsers(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getInactiveUsers(): Collection
    {
        return $this->model->where('is_active', false)->get();
    }

    public function countByStatus(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->where('is_active', true)->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
        ];
    }

    public function emailExists(string $email, ?string $excludeId = null): bool
    {
        $query = $this->model->where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    public function getUsersWithPermissions(): Collection
    {
        return $this->model->with(['roles.permissions'])->get();
    }

    public function assignRole(string $userId, string $roleId): bool
    {
        $user = $this->findById($userId);
        
        if (!$user) {
            return false;
        }

        $role = UserRole::find($roleId);
        
        if (!$role) {
            return false;
        }

        $user->assignRole($role);
        return true;
    }

    public function removeRole(string $userId, string $roleId): bool
    {
        $user = $this->findById($userId);
        
        if (!$user) {
            return false;
        }

        $role = UserRole::find($roleId);
        
        if (!$role) {
            return false;
        }

        $user->removeRole($role);
        return true;
    }
    
    public function findByClientAndEmail(string $clientId, string $email): ?User
    {
        return $this->model
            ->where('client_id', $clientId)
            ->where('email', $email)
            ->first();
    }
}