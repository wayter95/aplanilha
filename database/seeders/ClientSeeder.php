<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClientSubscribe;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create a default client for testing
        ClientSubscribe::create([
            'name' => 'Empresa Demo',
            'subdomain' => 'demo',
            'email' => 'contato@empresademo.com',
            'cnpj' => '12.345.678/0001-90',
            'plan' => 'premium',
            'active' => true,
            'expires_at' => now()->addYear(),
        ]);
    }
}
