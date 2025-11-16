<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Services\Interfaces\DocumentTemplateServiceInterface;
use App\Services\Interfaces\DocumentTypeServiceInterface;
use App\Services\Interfaces\DocumentRenderServiceInterface;
use App\Services\Interfaces\DocumentPdfServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DocumentTemplateController extends Controller
{
    public function __construct(
        private ?DocumentTemplateServiceInterface $service = null,
        private ?DocumentTypeServiceInterface $typeService = null,
        private ?DocumentRenderServiceInterface $renderer = null,
        private ?DocumentPdfServiceInterface $pdf = null
    ) {
        $this->middleware('auth');
        $this->middleware('client.subscribe');
    }

    public function index(): Response
    {
        return Inertia::render('DocumentTemplates/Index');
    }

    public function create(string $tempId): Response
    {
        return Inertia::render('DocumentTemplates/Form', [
            'id' => null,
            'tempKey' => $tempId,
        ]);
    }

    public function edit(string $id): Response
    {
        return Inertia::render('DocumentTemplates/Form', [
            'id' => $id,
            'tempKey' => null,
        ]);
    }

    // ==================== API JSON Methods ====================

    /**
     * API: Listar templates por tipo
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $validCodes = $this->typeService->getCodes();
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', $validCodes),
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);
        $items = $this->service->listByType($validated['type'], $perPage);
        return response()->json($items);
    }

    /**
     * API: Criar novo template
     */
    public function store(Request $request): JsonResponse
    {
        $validCodes = $this->typeService->getCodes();
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', $validCodes),
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

        $client = $request->get('client_subscribe');
        $validated['client_id'] = $client->id;

        $created = $this->service->create($validated);

        if (!empty($validated['is_default'])) {
            $this->service->setDefault($created->id);
            $created = $this->service->find($created->id);
        }

        return response()->json($created, 201);
    }

    /**
     * API: Exibir template específico
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
     * API: Atualizar template
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validCodes = $this->typeService->getCodes();
        $validated = $request->validate([
            'type' => 'sometimes|string|in:' . implode(',', $validCodes),
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

    /**
     * API: Deletar template
     */
    public function destroy(string $id): JsonResponse
    {
        $ok = $this->service->delete($id);
        if (!$ok) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['message' => 'Deleted']);
    }

    /**
     * API: Definir template como padrão
     */
    public function setDefault(string $id): JsonResponse
    {
        $ok = $this->service->setDefault($id);
        if (!$ok) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $item = $this->service->find($id);
        return response()->json($item);
    }

    /**
     * API: Obter placeholders disponíveis
     */
    public function placeholders(Request $request): JsonResponse
    {
        $validCodes = $this->typeService->getCodes();
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', $validCodes),
        ]);

        $placeholders = [
            ['key' => '${name}', 'description' => 'Nome ou identificação principal'],
            ['key' => '${current_date}', 'description' => 'Data atual no momento da geração'],
        ];

        return response()->json($placeholders);
    }

    /**
     * API: Obter tipos de documento disponíveis
     */
    public function types(): JsonResponse
    {
        $types = $this->typeService->findActive();
        return response()->json($types->pluck('code')->toArray());
    }

    /**
     * API: Gerar preview HTML do documento
     */
    public function previewHtml(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        $template = DocumentTemplate::find($id);
        if (!$template) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $html = $this->renderer->renderHtml($template, $validated);
        return response()->json(['html' => $html]);
    }

    /**
     * API: Exportar documento como PDF
     */
    public function exportPdf(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        $template = DocumentTemplate::find($id);
        if (!$template) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $html = $this->renderer->renderHtml($template, $validated);
        $binary = $this->pdf->renderPdf($html);
        $filename = ($template->name ?: 'documento') . '.pdf';
        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
