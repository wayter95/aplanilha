<?php

namespace App\Http\Controllers\Api;

use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\DocumentTemplateServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    public function __construct(private DocumentTemplateServiceInterface $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', DocumentType::all()),
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);
        $items = $this->service->listByType($validated['type'], $perPage);
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', DocumentType::all()),
            'name' => 'required|string|max:255',
            'language' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:5',
            'is_default' => 'sometimes|boolean',
            'is_system' => 'sometimes|boolean',
            'status' => 'sometimes|string|in:active,inactive',
            'header_html' => 'nullable|string',
            'footer_html' => 'nullable|string',
            'content_html' => 'required|string',
            'layout_json' => 'nullable',
            'background_image_path' => 'nullable|string|max:255',
            'fonts_json' => 'nullable',
        ]);

        $validated['client_id'] = app('tenant.context')->getClientId() ?: (auth()->user()->client_id ?? null);

        $created = $this->service->create($validated);

        if (!empty($validated['is_default'])) {
            $this->service->setDefault($created->id);
            $created = $this->service->find($created->id);
        }

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
        $validated = $request->validate([
            'type' => 'sometimes|string|in:' . implode(',', DocumentType::all()),
            'name' => 'sometimes|string|max:255',
            'language' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:5',
            'is_default' => 'sometimes|boolean',
            'is_system' => 'sometimes|boolean',
            'status' => 'sometimes|string|in:active,inactive',
            'header_html' => 'nullable|string',
            'footer_html' => 'nullable|string',
            'content_html' => 'sometimes|string',
            'layout_json' => 'nullable',
            'background_image_path' => 'nullable|string|max:255',
            'fonts_json' => 'nullable',
        ]);

        $ok = $this->service->update($id, $validated);
        if (!$ok) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if (array_key_exists('is_default', $validated) && $validated['is_default']) {
            $this->service->setDefault($id);
        }

        $item = $this->service->find($id);
        return response()->json($item);
    }

    public function destroy(string $id): JsonResponse
    {
        $ok = $this->service->delete($id);
        if (!$ok) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['message' => 'Deleted']);
    }

    public function setDefault(string $id): JsonResponse
    {
        $ok = $this->service->setDefault($id);
        if (!$ok) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $item = $this->service->find($id);
        return response()->json($item);
    }

    public function placeholders(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', DocumentType::all()),
        ]);

        $placeholders = [
            ['key' => '${name}', 'description' => 'Nome ou identificação principal'],
            ['key' => '${current_date}', 'description' => 'Data atual no momento da geração'],
        ];

        return response()->json($placeholders);
    }

    public function types(): JsonResponse
    {
        return response()->json(DocumentType::all());
    }
}


