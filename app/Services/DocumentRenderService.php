<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Services\Interfaces\DocumentRenderServiceInterface;

class DocumentRenderService implements DocumentRenderServiceInterface
{
    public function renderHtml(DocumentTemplate $template, array $data = []): string
    {
        $map = [
            '${name}' => (string)($data['name'] ?? ''),
            '${current_date}' => date('d/m/Y'),
        ];

        $replace = function (?string $html) use ($map) {
            if (!$html) return '';
            return str_replace(array_keys($map), array_values($map), $html);
        };

        $bgStyle = $template->background_image_path ? "background-image:url('" . $template->background_image_path . "');background-size:cover;background-position:center;" : '';

        $fonts = is_array($template->fonts_json) ? $template->fonts_json : json_decode((string)$template->fonts_json, true) ?: [];
        $layout = is_array($template->layout_json) ? $template->layout_json : json_decode((string)$template->layout_json, true) ?: [];

        $fontFamily = $fonts['family'] ?? 'Inter, system-ui, sans-serif';
        $fontSize = (string)($fonts['size'] ?? 12) . 'px';
        $color = $fonts['color'] ?? '#111111';
        $textAlign = $layout['contentAlign'] ?? 'left';

        $innerStyle = "padding:48px;font-family:$fontFamily;font-size:$fontSize;color:$color;text-align:$textAlign;background-color:rgba(255,255,255,0.85);";

        $header = $replace($template->header_html);
        $content = $replace($template->content_html);
        $footer = $replace($template->footer_html);

        return <<<HTML
<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        .page { width: 794px; height: 1123px; margin: 0 auto; $bgStyle }
        .inner { $innerStyle }
    </style>
    <title>Document</title>
</head>
<body>
    <div class="page">
        <div class="inner">
            <div>$header</div>
            <div style="margin:16px 0;">$content</div>
            <div>$footer</div>
        </div>
    </div>
</body>
</html>
HTML;
    }
}


