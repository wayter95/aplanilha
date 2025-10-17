<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function all();
    public function find(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function paginate(int $perPage = 15);
}
