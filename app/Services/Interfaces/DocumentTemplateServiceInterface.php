<?php

namespace App\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface DocumentTemplateServiceInterface
{
    public function create(array $data): Model;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function find(string $id): ?Model;
    public function listByType(string $type, int $perPage = 15): LengthAwarePaginator;
    public function setDefault(string $id): bool;
}


