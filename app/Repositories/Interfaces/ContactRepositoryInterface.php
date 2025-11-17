<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findByType(string $type): Collection;
    public function findByCity(string $city): Collection;
    public function findByCountry(string $country): Collection;
    public function searchContacts(string $search, int $perPage = 15): LengthAwarePaginator;
}
