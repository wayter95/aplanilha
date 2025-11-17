<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\DocumentTypeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class DocumentTypeController extends Controller
{
    public function __construct(private DocumentTypeServiceInterface $service)
    {
        $this->middleware('auth');
        $this->middleware('client.subscribe');
    }

    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
            ];

            $client = $request->get('client_subscribe');

            $types = $this->service->getByClientPaginated($client->id, $perPage, $filters);

            return Inertia::render('DocumentTypes/Index', [
                'types' => $types,
                'filters' => $filters,
            ]);
        } catch (Exception $e) {
            return Inertia::render('DocumentTypes/Index', [
                'types' => ['data' => [], 'current_page' => 1, 'total' => 0],
                'filters' => [],
                'error' => 'Erro ao carregar tipos: ' . $e->getMessage(),
            ]);
        }
    }

    public function exportCsv(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
            ];

            $types = $this->service->getAll($filters);

            $csvContent = "ID,Nome,Código,Descrição,Status,Ordem,Data de Criação\n";
            
            foreach ($types as $type) {
                $status = $type->is_active ? 'Ativo' : 'Inativo';
                
                $csvContent .= sprintf(
                    "%s,%s,%s,%s,%s,%s,%s\n",
                    $type->id,
                    $type->name,
                    $type->code,
                    $type->description ?? '',
                    $status,
                    $type->sort_order,
                    $type->created_at->format('d/m/Y H:i:s')
                );
            }

            return response()->json([
                'success' => true,
                'csv_content' => $csvContent,
                'filename' => 'tipos_documentos_' . date('Y-m-d_H-i-s') . '.csv',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function create(string $tempId): Response
    {
        return Inertia::render('DocumentTypes/Form', [
            'id' => null,
            'tempKey' => $tempId,
        ]);
    }

    public function edit(string $id): Response
    {
        return Inertia::render('DocumentTypes/Form', [
            'id' => $id,
            'tempKey' => null,
        ]);
    }

    // ==================== API JSON Methods ====================

    /**
     * API: Criar novo tipo de documento
     */
    public function store(Request $request): JsonResponse
    {
        $client = $request->get('client_subscribe');
        
        $rules = [
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('document_types', 'code')
                    ->where('client_id', $client->id)
                    ->whereNull('deleted_at')
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ];
        
        $validated = $request->validate($rules);

        $created = $this->service->create($validated);
        return response()->json($created, 201);
    }

    /**
     * API: Exibir tipo de documento específico
     */
    public function show(string $id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($item);
    }

    /**
     * API: Atualizar tipo de documento
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $client = $request->get('client_subscribe');
        
        $rules = [
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('document_types', 'code')
                    ->ignore($id)
                    ->where('client_id', $client->id)
                    ->whereNull('deleted_at')
            ],
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ];
        
        $validated = $request->validate($rules);

        $ok = $this->service->update($id, $validated);
        if (!$ok) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $item = $this->service->find($id);
        return response()->json($item);
    }

    /**
     * API: Deletar tipo de documento
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $ok = $this->service->delete($id);
            if (!$ok) {
                return response()->json(['message' => 'Not found'], 404);
            }
            return response()->json(['message' => 'Deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * API: Obter códigos de tipos de documento
     */
    public function codes(): JsonResponse
    {
        $codes = $this->service->getCodes();
        return response()->json($codes);
    }
}

