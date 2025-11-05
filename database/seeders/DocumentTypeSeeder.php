<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'contract',
                'name' => 'Contratos',
                'description' => 'Modelos de contratos',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'invoice',
                'name' => 'Faturas',
                'description' => 'Modelos de faturas',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'quote',
                'name' => 'Orçamentos',
                'description' => 'Modelos de orçamentos',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($types as $type) {
            DocumentType::withoutGlobalScopes()->updateOrCreate(
                [
                    'code' => $type['code'],
                    'client_id' => null
                ],
                array_merge($type, [
                    'id' => Str::uuid()->toString(),
                    'client_id' => null
                ])
            );
        }
    }
}
