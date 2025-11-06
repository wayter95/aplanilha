<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $client = null;
            
            // Serializa apenas os dados necessários do usuário para evitar problemas com TenantScope
            $userData = null;
            if ($user) {
                try {
                    $userData = [
                        'id' => $user->id,
                        'name' => $user->name ?? null,
                        'email' => $user->email ?? null,
                        'client_id' => $user->client_id ?? null,
                        'is_master' => $user->is_master ?? false,
                    ];
                } catch (\Exception $e) {
                    // Se houver erro ao acessar propriedades do usuário, deixa como null
                    \Log::warning('Erro ao serializar usuário no HomeController: ' . $e->getMessage());
                    $userData = null;
                }
            }
            
            try {
                $tenantContext = app('tenant.context');
                $client = $tenantContext->getClient();
            } catch (\Exception $e) {
                // Tenta obter tenant padrão, mas sem causar erro se não houver
                try {
                    $client = \App\Models\ClientSubscribe::withoutGlobalScopes()->first();
                } catch (\Exception $e2) {
                    // Se não conseguir, deixa como null
                    $client = null;
                }
            }

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

            $clientData = null;
            if ($client) {
                try {
                    $clientData = [
                        'id' => $client->id,
                        'name' => $client->name ?? null,
                    ];
                } catch (\Exception $e) {
                    \Log::warning('Erro ao serializar client no HomeController: ' . $e->getMessage());
                    $clientData = null;
                }
            }

            return Inertia::render('Home', [
                'title' => 'Dashboard',
                'description' => 'Visão geral do sistema',
                'user' => $userData,
                'stats' => $stats,
                'recentActivities' => $recentActivities,
                'client' => $clientData,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro no HomeController::index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Retorna uma resposta de erro mais amigável
            return Inertia::render('Home', [
                'title' => 'Dashboard',
                'description' => 'Visão geral do sistema',
                'user' => null,
                'stats' => [
                    'totalUsers' => 0,
                    'activeUsers' => 0,
                    'admins' => 0,
                    'todayLogins' => 0,
                ],
                'recentActivities' => [],
                'client' => null,
                'error' => 'Erro ao carregar dados do dashboard',
            ]);
        }
    }
}
