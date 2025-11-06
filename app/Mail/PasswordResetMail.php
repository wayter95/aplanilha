<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PasswordResetMail extends Mailable
{
	use Queueable, SerializesModels;

	public User $user;
	public string $token;
	public string $clientId;
	public string $resetUrl;

	
	public function __construct(User $user, string $token, string $clientId)
	{
		$this->user = $user;
		$this->token = $token;
		$this->clientId = $clientId;

		$subdomain = $user->client ? $user->client->subdomain : 'app';
		$this->resetUrl = "https://{$subdomain}.seusistema.com/password/reset?token={$token}&email={$user->email}";
	}

	public function build()
	{
		return $this->subject('Recuperação de senha')
			->view('emails.password_reset')
			->with([
				'user' => $this->user,
				'resetUrl' => $this->resetUrl,
			]);
	}
}
