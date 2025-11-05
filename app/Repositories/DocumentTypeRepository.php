<?php

namespace App\Repositories;

use App\Models\DocumentType;
use App\Repositories\Interfaces\DocumentTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

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
}

