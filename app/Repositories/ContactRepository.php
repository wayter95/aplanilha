<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    public function __construct(Contact $model)
    {
        parent::__construct($model);
    }

    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['responsibleUser', 'contactPersons']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (!empty($filters['city'])) {
            $query->byCity($filters['city']);
        }

        if (!empty($filters['country'])) {
            $query->byCountry($filters['country']);
        }

        if (!empty($filters['responsible_user_id'])) {
            $query->where('responsible_user_id', $filters['responsible_user_id']);
        }

        if (isset($filters['sort_by']) && isset($filters['sort_direction'])) {
            $query->orderBy($filters['sort_by'], $filters['sort_direction']);
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate($perPage);
    }

    public function findByType(string $type): Collection
    {
        return $this->model->byType($type)->get();
    }

    public function findByCity(string $city): Collection
    {
        return $this->model->byCity($city)->get();
    }

    public function findByCountry(string $country): Collection
    {
        return $this->model->byCountry($country)->get();
    }

    public function searchContacts(string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->search($search)
            ->with(['responsibleUser', 'contactPersons'])
            ->orderBy('name', 'asc')
            ->paginate($perPage);
    }
}
