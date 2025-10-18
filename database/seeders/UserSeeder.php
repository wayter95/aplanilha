<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ClientSubscribe;
use App\Models\UserRole;

class UserSeeder extends Seeder
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

        // Get roles
        $adminRole = UserRole::where('name', 'admin')->first();
        $userRole = UserRole::where('name', 'user')->first();

        // Create users
        $users = [
            [
                'client_id' => $client->id,
                'name' => 'João Silva',
                'email' => 'joao.silva@empresademo.com',
                'password' => Hash::make('password123'),
                'is_master' => false,
                'role_id' => $adminRole->id,
            ],
            [
                'client_id' => $client->id,
                'name' => 'Maria Santos',
                'email' => 'maria.santos@empresademo.com',
                'password' => Hash::make('password123'),
                'is_master' => false,
                'role_id' => $userRole->id,
            ],
            [
                'client_id' => $client->id,
                'name' => 'Pedro Costa',
                'email' => 'pedro.costa@empresademo.com',
                'password' => Hash::make('password123'),
                'is_master' => false,
                'role_id' => $userRole->id,
            ],
            [
                'client_id' => $client->id,
                'name' => 'Ana Oliveira',
                'email' => 'ana.oliveira@empresademo.com',
                'password' => Hash::make('password123'),
                'is_master' => false,
                'role_id' => $userRole->id,
            ],
        ];

        foreach ($users as $userData) {
            $roleId = $userData['role_id'];
            unset($userData['role_id']);
            
            $user = User::create($userData);
            
            // Assign role
            if ($roleId) {
                $role = UserRole::find($roleId);
                $user->assignRole($role);
            }
        }
    }
}