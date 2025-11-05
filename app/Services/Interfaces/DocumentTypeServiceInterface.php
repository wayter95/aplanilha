<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface DocumentTypeServiceInterface
{
    public function create(array $data): Model;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function find(string $id): ?Model;
    public function findActive(): Collection;
    public function findByCode(string $code): ?Model;
    public function getCodes(): array;
    public function validateType(string $code): bool;
}

