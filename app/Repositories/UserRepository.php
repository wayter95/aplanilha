<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get all users with pagination.
     */
    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['roles', 'client']);

        // Apply filters
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

    /**
     * Get all users without pagination.
     */
    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->with(['roles', 'client']);

        // Apply filters
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

    /**
     * Find user by ID.
     */
    public function findById(string $id): ?User
    {
        return $this->model->with(['roles', 'client'])->find($id);
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * Update user by ID.
     */
    public function update(string $id, array $data): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    /**
     * Delete user by ID.
     */
    public function delete(string $id): bool
    {
        $user = $this->findById($id);
        
        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    /**
     * Get users by role.
     */
    public function getByRole(string $roleName): Collection
    {
        return $this->model->whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })->get();
    }

    /**
     * Get active users.
     */
    public function getActiveUsers(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    /**
     * Get inactive users.
     */
    public function getInactiveUsers(): Collection
    {
        return $this->model->where('is_active', false)->get();
    }

    /**
     * Count users by status.
     */
    public function countByStatus(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->where('is_active', true)->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
        ];
    }

    /**
     * Check if email exists.
     */
    public function emailExists(string $email, ?string $excludeId = null): bool
    {
        $query = $this->model->where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Get users with their roles and permissions.
     */
    public function getUsersWithPermissions(): Collection
    {
        return $this->model->with(['roles.permissions'])->get();
    }

    /**
     * Assign role to user.
     */
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

    /**
     * Remove role from user.
     */
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
}