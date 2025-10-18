<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        // Mock data for development
        $mockUsers = collect([
            (object)[
                'id' => '1',
                'name' => 'João Silva',
                'email' => 'joao.silva@example.com',
                'status' => 'Ativo',
                'is_master' => false,
                'created_at' => now()->subDays(5)->toISOString(),
                'updated_at' => now()->subDays(1)->toISOString(),
            ],
            (object)[
                'id' => '2',
                'name' => 'Maria Santos',
                'email' => 'maria.santos@example.com',
                'status' => 'Ativo',
                'is_master' => true,
                'created_at' => now()->subDays(10)->toISOString(),
                'updated_at' => now()->subHours(2)->toISOString(),
            ],
            (object)[
                'id' => '3',
                'name' => 'Pedro Costa',
                'email' => 'pedro.costa@example.com',
                'status' => 'Ativo',
                'is_master' => false,
                'created_at' => now()->subDays(3)->toISOString(),
                'updated_at' => now()->subHours(5)->toISOString(),
            ],
            (object)[
                'id' => '4',
                'name' => 'Ana Oliveira',
                'email' => 'ana.oliveira@example.com',
                'status' => 'Inativo',
                'is_master' => false,
                'created_at' => now()->subDays(15)->toISOString(),
                'updated_at' => now()->subDays(2)->toISOString(),
            ],
            (object)[
                'id' => '5',
                'name' => 'Carlos Lima',
                'email' => 'carlos.lima@example.com',
                'status' => 'Ativo',
                'is_master' => true,
                'created_at' => now()->subDays(7)->toISOString(),
                'updated_at' => now()->subMinutes(30)->toISOString(),
            ]
        ]);

        // Simulate pagination
        $users = (object)[
            'data' => $mockUsers->take(10),
            'total' => $mockUsers->count(),
            'from' => 1,
            'to' => $mockUsers->count(),
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => 10,
            'links' => [
                (object)['url' => null, 'label' => '&laquo; Anterior', 'active' => false],
                (object)['url' => '#', 'label' => '1', 'active' => true],
                (object)['url' => null, 'label' => 'Próximo &raquo;', 'active' => false],
            ]
        ];
        
        return Inertia::render('Users', [
            'users' => $users,
            'title' => 'Usuários',
            'description' => 'Gerenciar usuários do sistema'
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
            'title' => 'Novo Usuário',
            'description' => 'Criar um novo usuário'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_master' => 'boolean'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_master' => $validated['is_master'] ?? false,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function show(User $user)
    {
        // Mock data for development
        $mockUser = (object)[
            'id' => $user->id ?? '1',
            'name' => $user->name ?? 'João Silva',
            'email' => $user->email ?? 'joao.silva@example.com',
            'is_master' => $user->is_master ?? false,
            'created_at' => $user->created_at ?? now()->subDays(5)->toISOString(),
            'updated_at' => $user->updated_at ?? now()->subDays(1)->toISOString(),
        ];
        
        return Inertia::render('Users/Show', [
            'user' => $mockUser,
            'title' => 'Detalhes do Usuário',
            'description' => 'Visualizar informações do usuário'
        ]);
    }

    public function edit(User $user)
    {
        // Mock data for development
        $mockUser = (object)[
            'id' => $user->id ?? '1',
            'name' => $user->name ?? 'João Silva',
            'email' => $user->email ?? 'joao.silva@example.com',
            'is_master' => $user->is_master ?? false,
            'created_at' => $user->created_at ?? now()->subDays(5)->toISOString(),
            'updated_at' => $user->updated_at ?? now()->subDays(1)->toISOString(),
        ];
        
        return Inertia::render('Users/Edit', [
            'user' => $mockUser,
            'title' => 'Editar Usuário',
            'description' => 'Editar informações do usuário'
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_master' => 'boolean'
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_master' => $validated['is_master'] ?? false,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
