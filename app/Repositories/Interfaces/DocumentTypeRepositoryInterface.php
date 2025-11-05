<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface DocumentTypeRepositoryInterface extends BaseRepositoryInterface
{
    public function findActive(): Collection;
    public function findByCode(string $code): ?\Illuminate\Database\Eloquent\Model;
    public function getCodes(): array;
    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function getAll(array $filters = []): Collection;
}

