<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Signin', [
            'title' => 'Sign In',
            'description' => 'Sign in to your account',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only(['email', 'password']);
        $remember = $request->boolean('remember');

        $user = $this->findUserForTenant($credentials['email']);

        if (!$user) {
            return back()->withErrors([
                'email' => 'Usuário não encontrado.',
            ]);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('signin');
    }

    public function showForgotPassword()
    {
        return Inertia::render('Auth/ForgotPassword', [
            'title' => 'Esqueci minha senha',
            'description' => 'Recuperar senha',
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = $this->findUserForTenant($request->email);

        if (!$user) {
            return back()->withErrors([
                'email' => 'Usuário não encontrado.',
            ]);
        }

        $status = Password::sendResetLink(
            ['email' => $request->email]
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'title' => 'Redefinir senha',
            'description' => 'Defina uma nova senha',
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $this->findUserForTenant($request->email);

        if (!$user) {
            return back()->withErrors([
                'email' => 'Usuário não encontrado.',
            ]);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('signin')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    private function findUserForTenant(string $email): ?User
    {
        try {
            $tenantContext = app('tenant.context');
            $clientId = $tenantContext->getClientId();
        } catch (\Exception $e) {
            $defaultTenant = \App\Models\ClientSubscribe::first();
            $clientId = $defaultTenant ? $defaultTenant->id : null;
        }

        if ($clientId) {
            return User::where('email', $email)
                ->where('client_id', $clientId)
                ->first();
        }

        return User::where('email', $email)->first();
    }
}
