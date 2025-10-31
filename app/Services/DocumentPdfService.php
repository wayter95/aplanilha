<?php

namespace App\Services;

use App\Services\Interfaces\DocumentPdfServiceInterface;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentPdfService implements DocumentPdfServiceInterface
{
    public function renderPdf(string $html): string
    {
        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
        return $pdf->output();
    }
}


