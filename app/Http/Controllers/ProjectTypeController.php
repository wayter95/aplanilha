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
    }

    /**
     * Exibir listagem de tipos de projeto
     */
    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
            ];

            $clientId = app('tenant.context')->getClientId();
            if (!$clientId) {
                $clientId = Auth::user()?->client_id;
            }

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
            $clientId = app('tenant.context')->getClientId();
            if (!$clientId) {
                $clientId = Auth::user()?->client_id;
            }

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
    public function edit(string $id): Response
    {
        return Inertia::render('ProjectTypes/Form', [
            'mode' => 'edit',
            'id' => $id,
        ]);
    }
}
