<?php

namespace App\Repositories;

use App\Models\DocumentTemplate;
use App\Repositories\Interfaces\DocumentTemplateRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplateRepository extends BaseRepository implements DocumentTemplateRepositoryInterface
{
    public function __construct(DocumentTemplate $model)
    {
        parent::__construct($model);
    }

    public function listByType(string $type, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('type', $type)->orderBy('name')->paginate($perPage);
    }

    public function unsetDefaultForClientAndType(string $clientId, string $type): int
    {
        return $this->model->withoutGlobalScopes()->where('client_id', $clientId)->where('type', $type)->where('is_default', true)->update(['is_default' => false]);
    }

    public function findDefault(string $clientId, string $type): ?Model
    {
        return $this->model->withoutGlobalScopes()->where('client_id', $clientId)->where('type', $type)->where('is_default', true)->first();
    }
}


