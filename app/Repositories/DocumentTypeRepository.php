<?php

namespace App\Repositories;

use App\Models\DocumentType;
use App\Repositories\Interfaces\DocumentTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DocumentTypeRepository extends BaseRepository implements DocumentTypeRepositoryInterface
{
    public function __construct(DocumentType $model)
    {
        parent::__construct($model);
    }

    public function findActive(): Collection
    {
        return $this->model->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    }

    public function findByCode(string $code): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->model->where('code', $code)->first();
    }

    public function getCodes(): array
    {
        return $this->model->pluck('code')->toArray();
    }

    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('code', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('is_active', $filters['status'] === 'Ativo');
        }

        return $query->orderBy('sort_order')->orderBy('name')->paginate($perPage);
    }

    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        // Inclui tipos do cliente E tipos globais (client_id = null)
        $query = $this->model->where(function ($q) use ($clientId) {
            $q->where('client_id', $clientId)
              ->orWhereNull('client_id');
        });

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('code', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('is_active', $filters['status'] === 'Ativo');
        }

        return $query->orderBy('sort_order')->orderBy('name')->paginate($perPage);
    }

    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('code', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('is_active', $filters['status'] === 'Ativo');
        }

        return $query->orderBy('sort_order')->orderBy('name')->get();
    }
}

