<?php


namespace App\Services\Interfaces;

use App\Models\User;


interface PasswordResetTokenServiceInterface
{
    /**
     * Generate a password reset token for a user within the tenant.
     *
     * @param User $user
     * @return string
     */
    public function generateToken(User $user): string;

    /**
     * Request a password reset for a user within the tenant.
     *
     * @param string $clientId
     * @param string $email
     * @return void
     */
    public function requestReset(string $clientId, string $email): void;

    /**
     * Reset the user's password using a valid token within the tenant.
     *
     * @param string $clientId
     * @param string $email
     * @param string $token
     * @param string $newPassword
     * @return bool
     */
    public function resetPassword(string $clientId, string $email, string $token, string $newPassword): bool;
    public function validateToken(User $user, string $token): bool;

    /**
     * Invalidate a password reset token for a user and tenant.
     *
     * @param User $user
     * @param string $token
     * @return void
     */
    public function invalidateToken(User $user, string $token): void;
}