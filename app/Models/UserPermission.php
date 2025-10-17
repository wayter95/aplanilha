<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\TenantScope;

class UserPermission extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'user_permissions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'client_id',
        'module',
        'action',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the client that owns the permission.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    /**
     * Get the roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(UserRole::class, 'permission_role', 'permission_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include permissions for a specific client.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope a query to only include permissions for a specific module.
     */
    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope a query to only include permissions for a specific action.
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Get the permission name in a readable format.
     */
    public function getNameAttribute(): string
    {
        return ucfirst($this->action) . ' ' . ucfirst($this->module);
    }

    /**
     * Get the permission identifier.
     */
    public function getIdentifierAttribute(): string
    {
        return $this->module . '.' . $this->action;
    }
}
