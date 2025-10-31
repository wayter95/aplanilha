<?php

namespace App\Enums;

enum DocumentType: string
{
    case CONTRACT = 'contract';
    case INVOICE = 'invoice';
    case QUOTE = 'quote';

    public static function all(): array
    {
        return [
            self::CONTRACT->value,
            self::INVOICE->value,
            self::QUOTE->value,
        ];
    }
}


