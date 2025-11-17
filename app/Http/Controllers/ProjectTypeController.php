<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ProjectTypeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class ProjectTypeController extends Controller
{
    public function __construct(private ProjectTypeServiceInterface $service)
    {
        $this->middleware('auth');
        $this->middleware('client.subscribe');
    }

    /**
     * Exibir listagem de tipos de projeto
     */
    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $client = $request->get('client_subscribe');
            $clientId = $client->id;

            $perPage = $request->get('per_page', 10);
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
            ];

            $types = $this->service->getProjectTypesByClient($clientId, $perPage, $filters);

            return Inertia::render('ProjectTypes/Index', [
                'types' => $types,
                'filters' => $filters,
            ]);
        } catch (Exception $e) {
            return Inertia::render('ProjectTypes/Index', [
                'types' => ['data' => [], 'current_page' => 1, 'total' => 0],
                'filters' => [],
                'error' => 'Erro ao carregar tipos de projeto: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Exportar tipos de projeto para CSV
     */
    public function exportCsv(Request $request): JsonResponse
    {
        try {
            $client = $request->get('client_subscribe');
            $clientId = $client->id;

            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
            ];

            $csvContent = $this->service->exportToCsv($clientId, $filters);

            return response()->json([
                'success' => true,
                'csv_content' => $csvContent,
                'filename' => 'tipos_projetos_' . date('Y-m-d_H-i-s') . '.csv',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Renderizar formulário de criação
     */
    public function create(string $tempId): Response
    {
        return Inertia::render('ProjectTypes/Form', [
            'mode' => 'create',
            'id' => null,
            'tempKey' => $tempId,
        ]);
    }

    /**
     * Renderizar formulário de edição
     */
    public function edit(string $id): Response|RedirectResponse
    {
        $projectType = $this->service->findById($id);
        
        if (!$projectType) {
            return redirect()->route('projects.types')
                ->with('error', 'Tipo de projeto não encontrado.');
        }
        
        return Inertia::render('ProjectTypes/Form', [
            'mode' => 'edit',
            'id' => $id,
            'projectType' => $projectType,
        ]);
    }

    // ==================== API JSON Methods ====================

    /**
     * API: Criar novo tipo de projeto
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $client = $request->get('client_subscribe');
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'color' => 'sometimes|string|max:7',
                'status' => 'sometimes|in:a,b',
            ]);

            $validated['client_id'] = $client->id;

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
     * API: Exibir tipo de projeto específico
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
     * API: Atualizar tipo de projeto
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
     * API: Deletar tipo de projeto
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
     * API: Ativar tipo de projeto
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
     * API: Bloquear tipo de projeto
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
