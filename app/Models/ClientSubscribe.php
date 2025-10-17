<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientSubscribe extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'client_subscribes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'subdomain',
        'email',
        'cnpj',
        'plan',
        'active',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the users for the client.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'client_id');
    }

    /**
     * Get the roles for the client.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'client_id');
    }

    /**
     * Get the permissions for the client.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(UserPermission::class, 'client_id');
    }

    /**
     * Get the activity logs for the client.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'client_id');
    }

    /**
     * Scope a query to only include active clients.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include expired clients.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Check if the client is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the client is active and not expired.
     */
    public function isActive(): bool
    {
        return $this->active && !$this->isExpired();
    }
}
