<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }
    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
            ];

            $roles = $this->roleService->getAllRoles($perPage, $filters);
            $availablePermissions = $this->roleService->getAvailablePermissions();

            return Inertia::render('Roles', [
                'roles' => $roles,
                'filters' => $filters,
                'user' => Auth::user(),
                'availablePermissions' => $availablePermissions,
            ]);
        } catch (Exception $e) {
            return Inertia::render('Roles', [
                'roles' => [],
                'filters' => [],
                'user' => Auth::user(),
                'availablePermissions' => [],
                'error' => 'Erro ao carregar funções: ' . $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:user_permissions,id',
                'is_active' => 'boolean',
            ]);

            $role = $this->roleService->createRole($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Função criada com sucesso!',
                'role' => $role,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(string $id): Response|RedirectResponse
    {
        try {
            $role = $this->roleService->getRoleById($id);

            if (!$role) {
                abort(404, 'Função não encontrada');
            }

            return Inertia::render('Roles/Show', [
                'role' => $role,
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar função: ' . $e->getMessage());
        }
    }

    public function edit(string $id): Response
    {
        try {
            $role = $this->roleService->getRoleById($id);

            if (!$role) {
                abort(404, 'Função não encontrada');
            }

            $permissions = $this->roleService->getAvailablePermissions();

            return Inertia::render('Roles/Edit', [
                'role' => $role,
                'permissions' => $permissions,
            ]);
        } catch (Exception $e) {
            return Inertia::render('Roles/Edit', [
                'role' => null,
                'permissions' => [],
                'error' => 'Erro ao carregar função: ' . $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:user_permissions,id',
                'is_active' => 'boolean',
            ]);

            $role = $this->roleService->updateRole($id, $validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Função atualizada com sucesso!',
                'role' => $role,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->roleService->deleteRole($id);

            return response()->json([
                'success' => true,
                'message' => 'Função excluída com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function toggleStatus(string $id): JsonResponse
    {
        try {
            $role = $this->roleService->toggleRoleStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Status da função alterado com sucesso!',
                'role' => $role,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->roleService->getRoleStatistics();

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

    public function permissions(): JsonResponse
    {
        try {
            $permissions = $this->roleService->getAvailablePermissions();

            return response()->json([
                'success' => true,
                'data' => $permissions,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportCsv(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
            ];

            $roles = $this->roleService->getAllRolesList($filters);

            $csvContent = "ID,Nome,Display Name,Descrição,Status,Data de Criação\n";
            
            foreach ($roles as $role) {
                $status = $role->is_active ? 'Ativo' : 'Inativo';
                
                $csvContent .= sprintf(
                    "%s,%s,%s,%s,%s,%s\n",
                    $role->id,
                    $role->name,
                    $role->display_name,
                    $role->description,
                    $status,
                    $role->created_at->format('d/m/Y H:i:s')
                );
            }

            return response()->json([
                'success' => true,
                'csv_content' => $csvContent,
                'filename' => 'funcoes_' . date('Y-m-d_H-i-s') . '.csv',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
