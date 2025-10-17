<?php

namespace Database\Seeders;

use App\Models\ClientSubscribe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClientSubscribeSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'id' => Str::uuid(),
                'name' => 'Cliente Demo',
                'subdomain' => 'demo',
                'email' => 'demo@aplanilha.com',
                'cnpj' => '12.345.678/0001-90',
                'plan' => 'demo',
                'active' => true,
                'expires_at' => now()->addYear(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Empresa Teste',
                'subdomain' => 'teste',
                'email' => 'teste@aplanilha.com',
                'cnpj' => '98.765.432/0001-10',
                'plan' => 'premium',
                'active' => true,
                'expires_at' => now()->addMonths(6),
            ],
        ];

        foreach ($clients as $clientData) {
            ClientSubscribe::updateOrCreate(
                ['subdomain' => $clientData['subdomain']],
                $clientData
            );
        }
    }
}
