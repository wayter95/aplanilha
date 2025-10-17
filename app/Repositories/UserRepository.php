<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email, ?string $clientId = null): ?User
    {
        $query = $this->model->where('email', $email);
        
        if ($clientId !== null) {
            $query->where('client_id', $clientId);
        }
        
        return $query->first();
    }

    public function getByClient(string $clientId): Collection
    {
        return $this->model->forClient($clientId)->get();
    }

    public function getMasterUsers(): Collection
    {
        return $this->model->masters()->get();
    }

    public function getTenantUsers(string $clientId): Collection
    {
        return $this->model->tenantUsers()->forClient($clientId)->get();
    }

    public function assignRole(User $user, string $roleId): void
    {
        $user->roles()->syncWithoutDetaching([$roleId]);
    }

    public function removeRole(User $user, string $roleId): void
    {
        $user->roles()->detach($roleId);
    }

    public function hasRole(User $user, string $roleName): bool
    {
        return $user->hasRole($roleName);
    }

    public function hasPermission(User $user, string $module, string $action): bool
    {
        return $user->hasPermission($module, $action);
    }
}
