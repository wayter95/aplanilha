<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'client_id',
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        
        return null;
    }

    public static function createLog(
        string $action,
        string $description,
        ?string $userId = null,
        ?string $modelType = null,
        ?string $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        $user = Auth::user() ?? null;
        
        return static::create([
            'client_id' => $user?->client_id,
            'user_id' => $userId ?? $user?->id,
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getFormattedOldValuesAttribute(): ?string
    {
        if (!$this->old_values) {
            return null;
        }

        return json_encode($this->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function getFormattedNewValuesAttribute(): ?string
    {
        if (!$this->new_values) {
            return null;
        }

        return json_encode($this->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}