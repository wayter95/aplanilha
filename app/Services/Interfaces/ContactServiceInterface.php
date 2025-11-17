<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactServiceInterface
{
    public function create(array $data): Model;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function find(string $id): ?Model;
    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findByType(string $type): Collection;
    public function findByCity(string $city): Collection;
    public function findByCountry(string $country): Collection;
    public function searchContacts(string $search, int $perPage = 15): LengthAwarePaginator;
    public function getAvailableCities(): array;
    public function getAvailableCountries(): array;
}
