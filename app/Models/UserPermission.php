<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Scopes\TenantScope;

class UserPermission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'client_id',
        'module',
        'action',
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
        static::addGlobalScope(new TenantScope);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(UserRole::class, 'permission_role', 'permission_id', 'role_id')
                    ->withTimestamps();
    }

    public function client()
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function getIdentifierAttribute(): string
    {
        return $this->module . '.' . $this->action;
    }
}