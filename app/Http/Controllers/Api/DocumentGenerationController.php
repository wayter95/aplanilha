<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Services\Interfaces\DocumentRenderServiceInterface;
use App\Services\Interfaces\DocumentPdfServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentGenerationController extends Controller
{
    public function __construct(private DocumentRenderServiceInterface $renderer, private DocumentPdfServiceInterface $pdf)
    {
    }

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


