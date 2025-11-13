<?php

namespace App\Repositories;

use App\Models\ProjectType;
use App\Repositories\Interfaces\ProjectTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectTypeRepository extends BaseRepository implements ProjectTypeRepositoryInterface
{
    public function __construct(ProjectType $model)
    {
        parent::__construct($model);
    }

    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('client_id', $clientId);

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $statusValue = $filters['status'] === 'Ativo' ? 'a' : 'b';
            $query->where('status', $statusValue);
        }

        return $query->orderBy('title')->paginate($perPage);
    }

    public function getAllByClient(string $clientId, array $filters = []): Collection
    {
        $query = $this->model->where('client_id', $clientId);

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $statusValue = $filters['status'] === 'Ativo' ? 'a' : 'b';
            $query->where('status', $statusValue);
        }

        return $query->orderBy('title')->get();
    }

    public function titleExistsForClient(string $title, string $clientId, ?string $excludeId = null): bool
    {
        $query = $this->model
            ->where('client_id', $clientId)
            ->where('title', $title);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function getActiveTypes(string $clientId): Collection
    {
        return $this->model
            ->where('client_id', $clientId)
            ->where('status', 'a')
            ->orderBy('title')
            ->get();
    }
}
