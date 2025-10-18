<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserRole;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users with pagination and filters.
     */
    public function getAllUsers(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->userRepository->getAllPaginated($perPage, $filters);
    }

    /**
     * Get all users without pagination.
     */
    public function getAllUsersList(array $filters = []): Collection
    {
        return $this->userRepository->getAll($filters);
    }

    /**
     * Get user by ID.
     */
    public function getUserById(string $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Create a new user.
     */
    public function createUser(array $data): User
    {
        // Validate email uniqueness
        if ($this->userRepository->emailExists($data['email'])) {
            throw new \Exception('Email já está em uso.');
        }

        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Set default values
        $data['is_active'] = $data['is_active'] ?? true;
        $data['is_master'] = $data['is_master'] ?? false;

        // Create user
        $user = $this->userRepository->create($data);

        // Assign role if provided
        if (isset($data['role_id']) && $data['role_id']) {
            $this->userRepository->assignRole($user->id, $data['role_id']);
        }

        return $user;
    }

    /**
     * Update user by ID.
     */
    public function updateUser(string $id, array $data): User
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado.');
        }

        // Validate email uniqueness (excluding current user)
        if (isset($data['email']) && $this->userRepository->emailExists($data['email'], $id)) {
            throw new \Exception('Email já está em uso.');
        }

        // Hash password if provided
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Remove password from data if empty
            unset($data['password']);
        }

        // Update user
        $this->userRepository->update($id, $data);

        // Update role if provided
        if (isset($data['role_id'])) {
            // Remove all current roles
            $user->roles()->detach();
            
            // Assign new role
            if ($data['role_id']) {
                $this->userRepository->assignRole($id, $data['role_id']);
            }
        }

        return $this->userRepository->findById($id);
    }

    /**
     * Delete user by ID.
     */
    public function deleteUser(string $id): bool
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado.');
        }

        // Prevent deletion of master users
        if ($user->isMaster()) {
            throw new \Exception('Usuários master não podem ser excluídos.');
        }

        return $this->userRepository->delete($id);
    }

    /**
     * Get users by role.
     */
    public function getUsersByRole(string $roleName): Collection
    {
        return $this->userRepository->getByRole($roleName);
    }

    /**
     * Get active users.
     */
    public function getActiveUsers(): Collection
    {
        return $this->userRepository->getActiveUsers();
    }

    /**
     * Get inactive users.
     */
    public function getInactiveUsers(): Collection
    {
        return $this->userRepository->getInactiveUsers();
    }

    /**
     * Get user statistics.
     */
    public function getUserStatistics(): array
    {
        $stats = $this->userRepository->countByStatus();
        
        // Add role statistics
        $roleStats = UserRole::withCount('users')->get()->map(function ($role) {
            return [
                'name' => $role->name,
                'count' => $role->users_count,
            ];
        });

        return [
            'total' => $stats['total'],
            'active' => $stats['active'],
            'inactive' => $stats['inactive'],
            'roles' => $roleStats,
        ];
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleUserStatus(string $id): User
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado.');
        }

        // Prevent deactivating master users
        if ($user->isMaster() && $user->is_active) {
            throw new \Exception('Usuários master não podem ser desativados.');
        }

        $user->update(['is_active' => !$user->is_active]);
        
        return $user;
    }

    /**
     * Upload user avatar.
     */
    public function uploadAvatar(string $userId, UploadedFile $file): string
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado.');
        }

        // Delete old avatar if exists
        if ($user->avatar_path && Storage::exists($user->avatar_path)) {
            Storage::delete($user->avatar_path);
        }

        // Store new avatar
        $path = $file->store('avatars', 'public');
        
        // Update user avatar path
        $user->update(['avatar_path' => $path]);
        
        return $path;
    }

    /**
     * Remove user avatar.
     */
    public function removeAvatar(string $userId): bool
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado.');
        }

        if ($user->avatar_path && Storage::exists($user->avatar_path)) {
            Storage::delete($user->avatar_path);
        }

        $user->update(['avatar_path' => null]);
        
        return true;
    }

    /**
     * Get users with permissions.
     */
    public function getUsersWithPermissions(): Collection
    {
        return $this->userRepository->getUsersWithPermissions();
    }

    /**
     * Assign role to user.
     */
    public function assignRole(string $userId, string $roleId): bool
    {
        return $this->userRepository->assignRole($userId, $roleId);
    }

    /**
     * Remove role from user.
     */
    public function removeRole(string $userId, string $roleId): bool
    {
        return $this->userRepository->removeRole($userId, $roleId);
    }

    /**
     * Get available roles.
     */
    public function getAvailableRoles(): Collection
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

        $query = UserRole::where('is_active', true);
        
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        return $query->get();
    }

    /**
     * Validate user data.
     */
    public function validateUserData(array $data, ?string $excludeId = null): array
    {
        $errors = [];

        // Validate email
        if (isset($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email inválido.';
            } elseif ($this->userRepository->emailExists($data['email'], $excludeId)) {
                $errors['email'] = 'Email já está em uso.';
            }
        }

        // Validate password
        if (isset($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Senha deve ter pelo menos 8 caracteres.';
            }
        }

        // Validate name
        if (isset($data['name']) && empty(trim($data['name']))) {
            $errors['name'] = 'Nome é obrigatório.';
        }

        return $errors;
    }
}