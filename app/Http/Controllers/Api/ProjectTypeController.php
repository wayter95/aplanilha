<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ProjectTypeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ProjectTypeController extends Controller
{
    public function __construct(private ProjectTypeServiceInterface $service)
    {
        $this->middleware('auth');
    }

    /**
     * Listar tipos de projeto
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $clientId = app('tenant.context')->getClientId() ?: Auth::user()->client_id;
            
            $activeOnly = $request->boolean('active_only', false);
            
            if ($activeOnly) {
                $types = $this->service->getAllProjectTypesByClient($clientId)
                    ->where('status', 'a')
                    ->values();
                return response()->json($types);
            }

            $types = $this->service->getAllProjectTypesByClient($clientId);
            return response()->json($types);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar novo tipo de projeto
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $clientId = app('tenant.context')->getClientId() ?: Auth::user()->client_id;
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'color' => 'sometimes|string|max:7',
                'status' => 'sometimes|in:a,b',
            ]);

            $validated['client_id'] = $clientId;

            $projectType = $this->service->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de projeto criado com sucesso!',
                'data' => $projectType,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Exibir tipo de projeto específico
     */
    public function show(string $id): JsonResponse
    {
        try {
            $projectType = $this->service->findById($id);
            
            if (!$projectType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de projeto não encontrado.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $projectType,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar tipo de projeto
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'color' => 'sometimes|string|max:7',
                'status' => 'sometimes|in:a,b',
            ]);

            $this->service->update($id, $validated);

            $projectType = $this->service->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de projeto atualizado com sucesso!',
                'data' => $projectType,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Deletar tipo de projeto
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de projeto excluído com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Ativar tipo de projeto
     */
    public function activate(string $id): JsonResponse
    {
        try {
            $this->service->activate($id);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de projeto ativado com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bloquear tipo de projeto
     */
    public function block(string $id): JsonResponse
    {
        try {
            $this->service->block($id);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de projeto bloqueado com sucesso!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
