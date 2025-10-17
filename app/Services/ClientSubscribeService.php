<?php

namespace App\Services;

use App\Models\ClientSubscribe;
use App\Repositories\Interfaces\ClientSubscribeRepositoryInterface;
use App\Services\Interfaces\ClientSubscribeServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class ClientSubscribeService implements ClientSubscribeServiceInterface
{
    public function __construct(
        private ClientSubscribeRepositoryInterface $clientRepository
    ) {}

    public function createClient(array $data): ClientSubscribe
    {
        return $this->clientRepository->create($data);
    }

    public function updateClient(string $id, array $data): ClientSubscribe
    {
        $client = $this->clientRepository->find($id);
        if (!$client) {
            throw new \Exception('Client not found');
        }
        
        $this->clientRepository->update($id, $data);
        return $this->clientRepository->find($id);
    }

    public function deleteClient(string $id): bool
    {
        return $this->clientRepository->delete($id);
    }

    public function getClient(string $id): ?ClientSubscribe
    {
        return $this->clientRepository->find($id);
    }

    public function getClientBySubdomain(string $subdomain): ?ClientSubscribe
    {
        return $this->clientRepository->findBySubdomain($subdomain);
    }

    public function getAllClients(): Collection
    {
        return $this->clientRepository->all();
    }

    public function getActiveClients(): Collection
    {
        return $this->clientRepository->getActiveClients();
    }

    public function activateClient(string $id): bool
    {
        return $this->clientRepository->update($id, ['active' => true]);
    }

    public function deactivateClient(string $id): bool
    {
        return $this->clientRepository->update($id, ['active' => false]);
    }

    public function isClientActive(string $id): bool
    {
        $client = $this->clientRepository->find($id);
        return $client ? $client->isActive() : false;
    }
}
