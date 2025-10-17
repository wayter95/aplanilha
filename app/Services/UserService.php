<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function createUser(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        return $this->userRepository->create($data);
    }

    public function updateUser(string $id, array $data): User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new \Exception('User not found');
        }
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $this->userRepository->update($id, $data);
        return $this->userRepository->find($id);
    }

    public function deleteUser(string $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function getUser(string $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function getUserByEmail(string $email, ?string $clientId = null): ?User
    {
        return $this->userRepository->findByEmail($email, $clientId);
    }

    public function getUsersByClient(string $clientId): Collection
    {
        return $this->userRepository->getByClient($clientId);
    }

    public function getMasterUsers(): Collection
    {
        return $this->userRepository->getMasterUsers();
    }

    public function authenticateUser(string $email, string $password, ?string $clientId = null): ?User
    {
        $user = $this->userRepository->findByEmail($email, $clientId);
        
        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }
        
        return null;
    }

    public function assignRoleToUser(string $userId, string $roleId): bool
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return false;
        }
        
        $this->userRepository->assignRole($user, $roleId);
        return true;
    }

    public function removeRoleFromUser(string $userId, string $roleId): bool
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return false;
        }
        
        $this->userRepository->removeRole($user, $roleId);
        return true;
    }

    public function userHasRole(string $userId, string $roleName): bool
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return false;
        }
        
        return $this->userRepository->hasRole($user, $roleName);
    }

    public function userHasPermission(string $userId, string $module, string $action): bool
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return false;
        }
        
        return $this->userRepository->hasPermission($user, $module, $action);
    }

    public function resetUserPassword(string $email, ?string $clientId = null): bool
    {
        $user = $this->userRepository->findByEmail($email, $clientId);
        if (!$user) {
            return false;
        }
        
        $status = Password::sendResetLink(['email' => $email]);
        return $status === Password::RESET_LINK_SENT;
    }
}
