<?php

namespace App\Services\Interfaces;

use App\Models\DocumentTemplate;

interface DocumentRenderServiceInterface
{
    public function renderHtml(DocumentTemplate $template, array $data = []): string;
}


