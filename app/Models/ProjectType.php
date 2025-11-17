<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectType extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'project_types';

    protected $fillable = [
        'title',
        'client_id',
        'color',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Relacionamento com client_subscribes
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientSubscribe::class, 'client_id');
    }

    /**
     * Relacionamento com projects
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_types_id');
    }

    /**
     * Scope para filtrar por status ativo
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'a');
    }

    /**
     * Scope para filtrar por status bloqueado
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', 'b');
    }

    /**
     * Accessor para retornar o status formatado
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'a' => 'Ativo',
            'b' => 'Bloqueado',
            default => 'Desconhecido',
        };
    }
}
