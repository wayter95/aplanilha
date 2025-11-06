<?php

namespace App\Repositories\Interfaces;

use App\Models\ClientSubscribe;

interface ClientSubscribeRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySubdomain(string $subdomain): ?ClientSubscribe;
    public function findByEmail(string $email): ?ClientSubscribe;
    public function findByCnpj(string $cnpj): ?ClientSubscribe;
    public function getActiveClients();
    public function getExpiredClients();
}
