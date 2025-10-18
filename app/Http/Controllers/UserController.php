<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'role' => $request->get('role'),
            ];

            $users = $this->userService->getAllUsers($perPage, $filters);

            return Inertia::render('Users', [
                'users' => $users,
                'filters' => $filters,
                'user' => Auth::user(),
            ]);
        } catch (Exception $e) {
            return Inertia::render('Users', [
                'users' => [],
                'filters' => [],
                'user' => Auth::user(),
                'error' => 'Erro ao carregar usuários: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:8',
                'role_id' => 'nullable|exists:user_roles,id',
                'is_active' => 'boolean',
            ]);

            $user = $this->userService->createUser($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Usuário criado com sucesso!',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response|RedirectResponse
    {
        try {
            $user = $this->userService->getUserById($id);

            if (!$user) {
                abort(404, 'Usuário não encontrado');
            }

            return Inertia::render('Users/Show', [
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar usuário: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        try {
            $user = $this->userService->getUserById($id);

            if (!$user) {
                abort(404, 'Usuário não encontrado');
            }

            $roles = $this->userService->getAvailableRoles();

            return Inertia::render('Users/Edit', [
                'user' => $user,
                'roles' => $roles,
            ]);
        } catch (Exception $e) {
            return Inertia::render('Users/Edit', [
                'user' => null,
                'roles' => [],
                'error' => 'Erro ao carregar usuário: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => 'nullable|string|min:8',
                'role_id' => 'nullable|exists:user_roles,id',
                'is_active' => 'boolean',
            ]);

            $user = $this->userService->updateUser($id, $validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso!',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);

            return response()->json([
                'success' => true,
                'message' => 'Usuário excluído com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $user = $this->userService->toggleUserStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Status do usuário alterado com sucesso!',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get user statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->userService->getUserStatistics();

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available roles.
     */
    public function roles(): JsonResponse
    {
        try {
            $roles = $this->userService->getAvailableRoles();

            return response()->json([
                'success' => true,
                'data' => $roles,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export users to CSV.
     */
    public function exportCsv(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'role' => $request->get('role'),
            ];

            $users = $this->userService->getAllUsersList($filters);

            // Generate CSV content
            $csvContent = "ID,Nome,Email,Status,Função,Data de Criação\n";
            
            foreach ($users as $user) {
                $roleName = $user->roles->first() ? $user->roles->first()->name : 'N/A';
                $status = $user->is_active ? 'Ativo' : 'Inativo';
                
                $csvContent .= sprintf(
                    "%s,%s,%s,%s,%s,%s\n",
                    $user->id,
                    $user->name,
                    $user->email,
                    $status,
                    $roleName,
                    $user->created_at->format('d/m/Y H:i:s')
                );
            }

            return response()->json([
                'success' => true,
                'csv_content' => $csvContent,
                'filename' => 'usuarios_' . date('Y-m-d_H-i-s') . '.csv',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}