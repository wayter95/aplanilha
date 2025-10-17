<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\TenantScope;

class UserRole extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'user_roles';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'client_id',
        'name',
        'description',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the client that owns the role.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    /**
     * Get the users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Get the permissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(UserPermission::class, 'permission_role', 'role_id', 'permission_id')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include roles for a specific client.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Check if the role has a specific permission.
     */
    public function hasPermission(string $module, string $action): bool
    {
        return $this->permissions()
                    ->where('module', $module)
                    ->where('action', $action)
                    ->exists();
    }

    /**
     * Give permission to the role.
     */
    public function givePermission(UserPermission $permission): void
    {
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Revoke permission from the role.
     */
    public function revokePermission(UserPermission $permission): void
    {
        $this->permissions()->detach($permission->id);
    }
}
