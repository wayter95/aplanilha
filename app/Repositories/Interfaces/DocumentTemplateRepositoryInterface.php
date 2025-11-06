<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DocumentTemplateRepositoryInterface extends BaseRepositoryInterface
{
    public function listByType(string $type, int $perPage = 15): LengthAwarePaginator;
    public function unsetDefaultForClientAndType(string $clientId, string $type): int;
    public function findDefault(string $clientId, string $type): ?Model;
}


