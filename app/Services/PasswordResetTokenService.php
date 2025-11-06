<?php

namespace App\Services;

use App\Repositories\PasswordResetTokenRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\PasswordResetToken;
use App\Models\ClientSubscribe;
use App\Models\ActivityLog;
use Carbon\Carbon;

class PasswordResetTokenService
{
    protected PasswordResetTokenRepository $tokenRepository;
    protected UserRepository $userRepository;

    public function __construct(
        PasswordResetTokenRepository $tokenRepository,
        UserRepository $userRepository
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->userRepository = $userRepository;
    }

    public function requestReset(string $clientId, string $email): void
    {
        $user = $this->userRepository->findByClientAndEmail($clientId, $email);
        if (!$user) {
            return;
        }

        $token = Str::random(64);

        $this->tokenRepository->deleteByClientAndEmail($clientId, $email);

        $this->tokenRepository->create([
            'client_id' => $clientId,
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        Mail::to($email)->send(new \App\Mail\PasswordResetMail($user, $token, $clientId));

        ActivityLog::create([
            'client_id' => $clientId,
            'user_id' => $user->id,
            'action' => 'password_reset_requested',
            'description' => 'Solicitação de recuperação de senha',
            'created_at' => Carbon::now(),
        ]);
    }

    public function resetPassword(string $clientId, string $email, string $token, string $newPassword): bool
    {
        $record = $this->tokenRepository->findByClientAndEmail($clientId, $email);

        if (
            !$record ||
            !Hash::check($token, $record->token) ||
            Carbon::parse($record->created_at)->addMinutes(60)->isPast()
        ) {
            return false;
        }

        $user = $this->userRepository->findByClientAndEmail($clientId, $email);
        if (!$user) {
            return false;
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        $this->tokenRepository->deleteByClientAndEmail($clientId, $email);

        ActivityLog::create([
            'client_id' => $clientId,
            'user_id' => $user->id,
            'action' => 'password_reset_completed',
            'description' => 'Senha redefinida com sucesso',
            'created_at' => Carbon::now(),
        ]);

        return true;
    }
}