<?php

namespace App\Services\Interfaces;

use App\Models\ClientSubscribe;
use Illuminate\Database\Eloquent\Collection;

interface ClientSubscribeServiceInterface
{
    public function createClient(array $data): ClientSubscribe;
    public function updateClient(string $id, array $data): ClientSubscribe;
    public function deleteClient(string $id): bool;
    public function getClient(string $id): ?ClientSubscribe;
    public function getClientBySubdomain(string $subdomain): ?ClientSubscribe;
    public function getAllClients(): Collection;
    public function getActiveClients(): Collection;
    public function activateClient(string $id): bool;
    public function deactivateClient(string $id): bool;
    public function isClientActive(string $id): bool;
}
