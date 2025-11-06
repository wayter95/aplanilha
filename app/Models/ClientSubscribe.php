<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ClientSubscribe extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'is_active',
        'subscription_plan',
        'subscription_start_date',
        'subscription_end_date',
        'max_users',
        'max_storage_gb',
        'features',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'subscription_start_date' => 'datetime',
            'subscription_end_date' => 'datetime',
            'features' => 'array',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'client_id');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'client_id');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(UserPermission::class, 'client_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithActiveSubscription($query)
    {
        return $query->where('is_active', true)
                    ->where('subscription_end_date', '>', now());
    }

    public function hasActiveSubscription(): bool
    {
        return $this->is_active && 
               $this->subscription_end_date && 
               $this->subscription_end_date->isFuture();
    }

    public function canAddUser(): bool
    {
        if (!$this->max_users) {
            return true;
        }

        return $this->users()->count() < $this->max_users;
    }

    public function hasFeature(string $feature): bool
    {
        if (!$this->features) {
            return false;
        }

        return in_array($feature, $this->features);
    }

    public function getSubscriptionStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if (!$this->subscription_end_date) {
            return 'active';
        }

        if ($this->subscription_end_date->isPast()) {
            return 'expired';
        }

        if ($this->subscription_end_date->diffInDays(now()) <= 7) {
            return 'expiring_soon';
        }

        return 'active';
    }

    public function getCurrentUserCountAttribute(): int
    {
        return $this->users()->count();
    }

    public function getUserUsagePercentageAttribute(): float
    {
        if (!$this->max_users) {
            return 0;
        }

        return ($this->current_user_count / $this->max_users) * 100;
    }
}