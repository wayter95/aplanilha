<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRole;
use App\Models\ClientSubscribe;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get the first client
        $client = ClientSubscribe::first();
        
        if (!$client) {
            return;
        }

        // Create default roles
        $roles = [
            [
                'client_id' => $client->id,
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Acesso total ao sistema',
                'is_active' => true,
            ],
            [
                'client_id' => $client->id,
                'name' => 'user',
                'display_name' => 'Usuário',
                'description' => 'Acesso basic ao sistema',
                'is_active' => true,
            ],
            [
                'client_id' => $client->id,
                'name' => 'moderator',
                'display_name' => 'Moderador',
                'description' => 'Acesso intermediário ao sistema',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            UserRole::create($role);
        }
    }
}