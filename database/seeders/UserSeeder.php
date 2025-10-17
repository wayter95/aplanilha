<?php

namespace Database\Seeders;

use App\Models\ClientSubscribe;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $masterUser = User::updateOrCreate(
            ['email' => 'master@aplanilha.com'],
            [
                'id' => Str::uuid(),
                'client_id' => null,
                'name' => 'Master User',
                'email' => 'master@aplanilha.com',
                'password' => Hash::make('password'),
                'is_master' => true,
                'email_verified_at' => now(),
            ]
        );

        $clients = ClientSubscribe::all();
        
        foreach ($clients as $client) {
            $adminRole = UserRole::where('client_id', $client->id)
                ->where('name', 'Admin')
                ->first();
            
            $userRole = UserRole::where('client_id', $client->id)
                ->where('name', 'User')
                ->first();

            $adminUser = User::updateOrCreate(
                [
                    'email' => "admin@{$client->subdomain}.com",
                    'client_id' => $client->id
                ],
                [
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'name' => "Admin {$client->name}",
                    'email' => "admin@{$client->subdomain}.com",
                    'password' => Hash::make('password'),
                    'is_master' => false,
                    'email_verified_at' => now(),
                ]
            );

            $demoUser = User::updateOrCreate(
                [
                    'email' => "demo@{$client->subdomain}.com",
                    'client_id' => $client->id
                ],
                [
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'name' => "Demo User {$client->name}",
                    'email' => "demo@{$client->subdomain}.com",
                    'password' => Hash::make('password'),
                    'is_master' => false,
                    'email_verified_at' => now(),
                ]
            );

            if ($adminRole) {
                $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
            }
            
            if ($userRole) {
                $demoUser->roles()->syncWithoutDetaching([$userRole->id]);
            }
        }
    }
}
