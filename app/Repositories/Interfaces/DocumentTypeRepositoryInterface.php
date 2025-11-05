<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface DocumentTypeRepositoryInterface extends BaseRepositoryInterface
{
    public function findActive(): Collection;
    public function findByCode(string $code): ?\Illuminate\Database\Eloquent\Model;
    public function getCodes(): array;
}

