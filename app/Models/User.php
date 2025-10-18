<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Scopes\TenantScope;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'name',
        'email',
        'password',
        'avatar_path',
        'is_master',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected static function booted(): void
    {
        // Temporarily disabled TenantScope for testing
        // static::addGlobalScope(new TenantScope);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_master' => 'boolean',
        ];
    }

    /**
     * Get the client that owns the user.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    /**
     * Get the roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(UserRole::class, 'role_user', 'user_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * Get the activity logs for the user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    /**
     * Scope a query to only include users for a specific client.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope a query to only include master users.
     */
    public function scopeMasters($query)
    {
        return $query->where('is_master', true);
    }

    /**
     * Scope a query to only include tenant users.
     */
    public function scopeTenantUsers($query)
    {
        return $query->where('is_master', false);
    }

    /**
     * Check if the user is a master user.
     */
    public function isMaster(): bool
    {
        return $this->is_master;
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $module, string $action): bool
    {
        return $this->roles()
                    ->whereHas('permissions', function ($query) use ($module, $action) {
                        $query->where('module', $module)
                              ->where('action', $action);
                    })
                    ->exists();
    }

    /**
     * Get all permissions for the user through their roles.
     */
    public function getAllPermissions()
    {
        return UserPermission::whereHas('roles', function ($query) {
            $query->whereHas('users', function ($userQuery) {
                $userQuery->where('user_id', $this->id);
            });
        })->get();
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(UserRole $role): void
    {
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(UserRole $role): void
    {
        $this->roles()->detach($role->id);
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar_path) {
            return null;
        }

        // Aqui você pode implementar a lógica para gerar URLs pré-assinadas do S3
        // Por enquanto, retornamos o caminho como está
        return $this->avatar_path;
    }
}
