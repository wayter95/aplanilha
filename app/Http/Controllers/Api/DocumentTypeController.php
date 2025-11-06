<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\DocumentTypeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentTypeController extends Controller
{
    public function __construct(private DocumentTypeServiceInterface $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $activeOnly = $request->boolean('active_only', false);
        
        $types = $activeOnly 
            ? $this->service->findActive() 
            : $this->service->findActive();

        return response()->json($types);
    }

    public function store(Request $request): JsonResponse
    {
        $clientId = app('tenant.context')->getClientId() ?: (auth()->user()->client_id ?? null);
        
        $rules = [
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('document_types', 'code')
                    ->where('client_id', $clientId)
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

    public function show(string $id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($item);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $clientId = app('tenant.context')->getClientId() ?: (auth()->user()->client_id ?? null);
        
        $rules = [
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('document_types', 'code')
                    ->ignore($id)
                    ->where('client_id', $clientId)
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

    public function codes(): JsonResponse
    {
        $codes = $this->service->getCodes();
        return response()->json($codes);
    }
}
