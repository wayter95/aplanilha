<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'status',
        'name',
        'project_number',
        'uf_project',
        'project_parent_id',
        'client_id',
        'project_types_id',
        'responsible_user_id',
        'user_manager_id',
        'start_date',
        'end_date',
        'client_contact_id',
        'location_contact_id',
    ];

    protected $casts = [
        'status' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
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
     * Relacionamento com project_types
     */
    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class, 'project_types_id');
    }

    /**
     * Relacionamento com usuário responsável
     */
    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    /**
     * Relacionamento com usuário gerente
     */
    public function managerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_manager_id');
    }

    /**
     * Relacionamento com contato do cliente
     */
    public function clientContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'client_contact_id');
    }

    /**
     * Relacionamento com contato da localização
     */
    public function locationContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'location_contact_id');
    }

    /**
     * Relacionamento com projeto pai (auto-relacionamento)
     */
    public function parentProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_parent_id');
    }

    /**
     * Relacionamento com sub-projetos (auto-relacionamento)
     */
    public function subProjects()
    {
        return $this->hasMany(Project::class, 'project_parent_id');
    }

    /**
     * Scope para filtrar por status ativo
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para filtrar por status pendente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para filtrar por status cancelado
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope para filtrar por status completo
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Accessor para retornar o status formatado
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Ativo',
            'pending' => 'Pendente',
            'cancelled' => 'Cancelado',
            'completed' => 'Completo',
            default => 'Desconhecido',
        };
    }

    /**
     * Verifica se o projeto está em andamento
     */
    public function isInProgress(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verifica se o projeto está completo
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Verifica se o projeto foi cancelado
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
