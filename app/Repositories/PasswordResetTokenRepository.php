<?php

namespace App\Repositories;

use App\Models\PasswordResetToken;
use App\Repositories\Interfaces\BaseRepositoryInterface;

class PasswordResetTokenRepository implements BaseRepositoryInterface
{
    protected PasswordResetToken $model;

    public function all()
    {
        return $this->model->all();
    }

    public function find(string $id): ?PasswordResetToken
    {
        return $this->model->where('id', $id)->first();
    }

    public function update(string $id, array $data): bool
    {
        $token = $this->find($id);
        if (!$token) {
            return false;
        }
        return $token->update($data);
    }

    public function delete(string $id): void
    {
        $this->model->where('id', $id)->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function __construct(PasswordResetToken $model)
    {
        $this->model = $model;
    }

    public function create(array $data): PasswordResetToken
    {
        if (empty($data['client_id'])) {
            throw new \InvalidArgumentException('client_id é obrigatório.');
        }
        return $this->model->create($data);
    }

    public function findByClientAndEmail(string $clientId, string $email): ?PasswordResetToken
    {
        return $this->model
            ->where('client_id', $clientId)
            ->where('email', $email)
            ->first();
    }

    public function deleteByClientAndEmail(string $clientId, string $email): void
    {
        $this->model
            ->where('client_id', $clientId)
            ->where('email', $email)
            ->delete();
    }

    public function validateToken(string $clientId, string $email, string $token): bool
    {
        $record = $this->model
            ->where('client_id', $clientId)
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        return $record !== null;
    }
}