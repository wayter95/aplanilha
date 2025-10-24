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

    public function getAllUsers(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->userRepository->getAllPaginated($perPage, $filters);
    }

    public function getAllUsersList(array $filters = []): Collection
    {
        return $this->userRepository->getAll($filters);
    }

    public function getUserById(string $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data): User
    {
        if ($this->userRepository->emailExists($data['email'])) {
            throw new \Exception('Email já está em uso.');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['is_active'] = $data['is_active'] ?? true;
        $data['is_master'] = $data['is_master'] ?? false;

        $user = $this->userRepository->create($data);

        if (isset($data['role_id']) && $data['role_id']) {
            $this->userRepository->assignRole($user->id, $data['role_id']);
        }

        return $user;
    }

    public function updateUser(string $id, array $data): User
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado.');
        }

        if (isset($data['email']) && $this->userRepository->emailExists($data['email'], $id)) {
            throw new \Exception('Email já está em uso.');
        }

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $this->userRepository->update($id, $data);

        if (isset($data['role_id'])) {
            $user->roles()->detach();
            
            if ($data['role_id']) {
                $this->userRepository->assignRole($id, $data['role_id']);
            }
        }

        return $this->userRepository->findById($id);
    }

    public function deleteUser(string $id): bool
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado.');
        }

        if ($user->isMaster()) {
            throw new \Exception('Usuários master não podem ser excluídos.');
        }

        return $this->userRepository->delete($id);
    }

    public function getUsersByRole(string $roleName): Collection
    {
        return $this->userRepository->getByRole($roleName);
    }

    public function getActiveUsers(): Collection
    {
        return $this->userRepository->getActiveUsers();
    }

    public function getInactiveUsers(): Collection
    {
        return $this->userRepository->getInactiveUsers();
    }

    public function getUserStatistics(): array
    {
        $stats = $this->userRepository->countByStatus();
        
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

    public function toggleUserStatus(string $id): User
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            throw new \Exception('Usuário não encontrado.');
        }

        if ($user->isMaster() && $user->is_active) {
            throw new \Exception('Usuários master não podem ser desativados.');
        }

        $user->update(['is_active' => !$user->is_active]);
        
        return $user;
    }

    // Métodos de avatar removidos - agora usando photo_key com S3

    public function getUsersWithPermissions(): Collection
    {
        return $this->userRepository->getUsersWithPermissions();
    }

    public function assignRole(string $userId, string $roleId): bool
    {
        return $this->userRepository->assignRole($userId, $roleId);
    }

    public function removeRole(string $userId, string $roleId): bool
    {
        return $this->userRepository->removeRole($userId, $roleId);
    }

    public function getAvailableRoles(): Collection
    {
        try {
            $tenantContext = app('tenant.context');
            $clientId = $tenantContext->getClientId();
        } catch (\Exception $e) {
            $defaultTenant = \App\Models\ClientSubscribe::first();
            $clientId = $defaultTenant ? $defaultTenant->id : null;
        }

        $query = UserRole::where('is_active', true);
        
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        return $query->get();
    }

    public function validateUserData(array $data, ?string $excludeId = null): array
    {
        $errors = [];

        if (isset($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email inválido.';
            } elseif ($this->userRepository->emailExists($data['email'], $excludeId)) {
                $errors['email'] = 'Email já está em uso.';
            }
        }

        if (isset($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Senha deve ter pelo menos 8 caracteres.';
            }
        }

        if (isset($data['name']) && empty(trim($data['name']))) {
            $errors['name'] = 'Nome obrigatório.';
        }

        return $errors;
    }
}