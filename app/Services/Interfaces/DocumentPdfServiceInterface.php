<?php

namespace App\Services\Interfaces;

use App\Models\DocumentTemplate;

interface DocumentPdfServiceInterface
{
    public function renderPdf(string $html): string;
}


