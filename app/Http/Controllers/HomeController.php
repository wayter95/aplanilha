<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenantContext = app('tenant.context');
        $client = $tenantContext->getClient();

        $stats = [
            'totalUsers' => 25,
            'activeUsers' => 23,
            'admins' => 3,
            'todayLogins' => 8,
        ];

        $recentActivities = [
            [
                'id' => '1',
                'action' => 'Login realizado',
                'created_at' => now()->subMinutes(5)->toISOString(),
                'user' => [
                    'name' => 'João Silva'
                ]
            ],
            [
                'id' => '2',
                'action' => 'Usuário criado',
                'created_at' => now()->subMinutes(15)->toISOString(),
                'user' => [
                    'name' => 'Maria Santos'
                ]
            ],
            [
                'id' => '3',
                'action' => 'Perfil atualizado',
                'created_at' => now()->subMinutes(30)->toISOString(),
                'user' => [
                    'name' => 'Pedro Costa'
                ]
            ],
            [
                'id' => '4',
                'action' => 'Senha alterada',
                'created_at' => now()->subHour()->toISOString(),
                'user' => [
                    'name' => 'Ana Oliveira'
                ]
            ],
            [
                'id' => '5',
                'action' => 'Login realizado',
                'created_at' => now()->subHours(2)->toISOString(),
                'user' => [
                    'name' => 'Carlos Lima'
                ]
            ],
        ];

        return Inertia::render('Home', [
            'title' => 'Dashboard',
            'description' => 'Visão geral do sistema',
            'user' => $user,
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'client' => $client,
        ]);
    }
}
