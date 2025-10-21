<?php

namespace App\Repositories\Interfaces;

use App\Models\PasswordResetToken;

interface PasswordResetTokenRepositoryInterface extends BaseRepositoryInterface
{
    public function create(array $data): PasswordResetToken;
    public function findByClientAndEmail(string $clientId, string $email): ?PasswordResetToken;
    public function deleteByClientAndEmail(string $clientId, string $email): void;
    public function validateToken(string $clientId, string $email, string $token): bool;
}