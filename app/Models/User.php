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
        'phone',
        'username',
        'photo_key',
        'is_master',
        'is_active',
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(UserRole::class, 'role_user', 'user_id', 'role_id')
                    ->withTimestamps();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeMasters($query)
    {
        return $query->where('is_master', true);
    }

    public function scopeTenantUsers($query)
    {
        return $query->where('is_master', false);
    }

    public function isMaster(): bool
    {
        return $this->is_master;
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermission(string $module, string $action): bool
    {
        return $this->roles()
                    ->whereHas('permissions', function ($query) use ($module, $action) {
                        $query->where('module', $module)
                              ->where('action', $action);
                    })
                    ->exists();
    }

    public function getAllPermissions()
    {
        return UserPermission::whereHas('roles', function ($query) {
            $query->whereHas('users', function ($userQuery) {
                $userQuery->where('user_id', $this->id);
            });
        })->get();
    }

    public function assignRole(UserRole $role): void
    {
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole(UserRole $role): void
    {
        $this->roles()->detach($role->id);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->photo_key) {
            return null;
        }

        // Retornar a URL pré-assinada para leitura
        // O frontend deve chamar o endpoint /api/uploads/signed-url?key={photo_key}
        return route('uploads.signed-url', ['key' => $this->photo_key]);
    }
}
