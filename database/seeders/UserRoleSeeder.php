<?php

namespace Database\Seeders;

use App\Models\ClientSubscribe;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $clients = ClientSubscribe::all();
        
        foreach ($clients as $client) {
            $roles = [
                [
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'name' => 'Admin',
                    'description' => 'Administrador completo do tenant',
                ],
                [
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'name' => 'User',
                    'description' => 'Usuário padrão com permissões limitadas',
                ],
            ];

            foreach ($roles as $roleData) {
                UserRole::updateOrCreate(
                    [
                        'client_id' => $roleData['client_id'],
                        'name' => $roleData['name']
                    ],
                    $roleData
                );
            }
        }
    }
}
