<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    public function __construct(Contact $model)
    {
        parent::__construct($model);
    }

    public function getByClientPaginated(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('client_id', $clientId)
            ->with(['responsibleUser']);

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function getAllByClient(string $clientId, array $filters = []): Collection
    {
        $query = $this->model->where('client_id', $clientId)
            ->with(['responsibleUser']);

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('name')->get();
    }

    public function getByType(string $clientId, string $type): Collection
    {
        return $this->model
            ->where('client_id', $clientId)
            ->where('type', $type)
            ->orderBy('name')
            ->get();
    }

    public function getCustomers(string $clientId): Collection
    {
        return $this->getByType($clientId, 'customer');
    }

    public function getSuppliers(string $clientId): Collection
    {
        return $this->getByType($clientId, 'supplier');
    }

    public function getLocations(string $clientId): Collection
    {
        return $this->getByType($clientId, 'location');
    }

    public function searchForSelect2(string $clientId, string $search = '', string $type = null, int $limit = 10): array
    {
        $query = $this->model->where('client_id', $clientId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        $contacts = $query->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'email', 'phone', 'type']);

        return $contacts->map(function ($contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'type' => $contact->type,
                'display_name' => $contact->name . ($contact->email ? ' (' . $contact->email . ')' : '')
            ];
        })->toArray();
    }
}
