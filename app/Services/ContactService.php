<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Services\Interfaces\ContactServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class ContactService implements ContactServiceInterface
{
    protected ContactRepositoryInterface $contactRepository;

    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function getContactsByClient(string $clientId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->contactRepository->getByClientPaginated($clientId, $perPage, $filters);
    }

    public function getAllContactsByClient(string $clientId): Collection
    {
        return $this->contactRepository->getAllByClient($clientId);
    }

    public function create(array $data): object
    {
        // Define tipo padrão como customer se não informado
        $data['type'] = $data['type'] ?? 'customer';

        return $this->contactRepository->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $contact = $this->contactRepository->find($id);
        
        if (!$contact) {
            throw new Exception('Contato não encontrado.');
        }

        return $this->contactRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        $contact = $this->contactRepository->find($id);
        
        if (!$contact) {
            throw new Exception('Contato não encontrado.');
        }

        return $this->contactRepository->delete($id);
    }

    public function findById(string $id): ?object
    {
        return $this->contactRepository->find($id);
    }

    public function getByType(string $clientId, string $type): Collection
    {
        return $this->contactRepository->getByType($clientId, $type);
    }

    public function getCustomers(string $clientId): Collection
    {
        return $this->contactRepository->getCustomers($clientId);
    }

    public function getSuppliers(string $clientId): Collection
    {
        return $this->contactRepository->getSuppliers($clientId);
    }

    public function getLocations(string $clientId): Collection
    {
        return $this->contactRepository->getLocations($clientId);
    }

    public function searchForSelect2(string $clientId, string $search = '', string $type = null, int $limit = 10): array
    {
        return $this->contactRepository->searchForSelect2($clientId, $search, $type, $limit);
    }
}
