<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('client_id', $clientId)
            ->with(['projectType', 'responsibleUser', 'managerUser', 'client']);

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('project_number', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['project_type']) && $filters['project_type']) {
            $query->where('project_types_id', $filters['project_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getAllByClient(string $clientId, array $filters = []): Collection
    {
        $query = $this->model->where('client_id', $clientId)
            ->with(['projectType', 'responsibleUser', 'managerUser', 'client']);

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('project_number', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['project_type']) && $filters['project_type']) {
            $query->where('project_types_id', $filters['project_type']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function projectNumberExistsForClient(string $projectNumber, string $clientId, ?string $excludeId = null): bool
    {
        $query = $this->model
            ->where('client_id', $clientId)
            ->where('project_number', $projectNumber);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function getActiveProjects(string $clientId): Collection
    {
        return $this->model
            ->where('client_id', $clientId)
            ->where('status', 'active')
            ->with(['projectType', 'responsibleUser', 'managerUser'])
            ->orderBy('name')
            ->get();
    }

    public function getByProjectType(string $projectTypeId): Collection
    {
        return $this->model
            ->where('project_types_id', $projectTypeId)
            ->with(['responsibleUser', 'managerUser', 'client'])
            ->orderBy('name')
            ->get();
    }

    public function getByStatus(string $clientId, string $status): Collection
    {
        return $this->model
            ->where('client_id', $clientId)
            ->where('status', $status)
            ->with(['projectType', 'responsibleUser', 'managerUser'])
            ->orderBy('name')
            ->get();
    }
}
