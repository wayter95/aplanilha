<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Scopes\TenantScope;

class UserRole extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'client_id',
        'name',
        'display_name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Temporarily disabled TenantScope for testing
        // static::addGlobalScope(new TenantScope);
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
     * Get the client that owns the role.
     */
    public function client()
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    /**
     * Scope a query to only include active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
     * Assign a permission to the role.
     */
    public function assignPermission(UserPermission $permission): void
    {
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Remove a permission from the role.
     */
    public function removePermission(UserPermission $permission): void
    {
        $this->permissions()->detach($permission->id);
    }
}