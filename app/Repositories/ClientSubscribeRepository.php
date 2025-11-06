<?php

namespace App\Repositories;

use App\Models\ClientSubscribe;
use App\Repositories\Interfaces\ClientSubscribeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ClientSubscribeRepository extends BaseRepository implements ClientSubscribeRepositoryInterface
{
    public function __construct(ClientSubscribe $model)
    {
        parent::__construct($model);
    }

    public function findBySubdomain(string $subdomain): ?ClientSubscribe
    {
        return $this->model->where('subdomain', $subdomain)->first();
    }

    public function findByEmail(string $email): ?ClientSubscribe
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByCnpj(string $cnpj): ?ClientSubscribe
    {
        return $this->model->where('cnpj', $cnpj)->first();
    }

    public function getActiveClients(): Collection
    {
        return $this->model->active()->get();
    }

    public function getExpiredClients(): Collection
    {
        return $this->model->expired()->get();
    }
}
