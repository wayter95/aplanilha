<?php

namespace Database\Seeders;

use App\Models\ClientSubscribe;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $clients = ClientSubscribe::all();
        
        foreach ($clients as $client) {
            $permissions = [
                ['module' => 'users', 'action' => 'view'],
                ['module' => 'users', 'action' => 'create'],
                ['module' => 'users', 'action' => 'edit'],
                ['module' => 'users', 'action' => 'delete'],
                ['module' => 'profile', 'action' => 'view'],
                ['module' => 'profile', 'action' => 'edit'],
                ['module' => 'dashboard', 'action' => 'view'],
                ['module' => 'reports', 'action' => 'view'],
                ['module' => 'reports', 'action' => 'create'],
                ['module' => 'settings', 'action' => 'view'],
                ['module' => 'settings', 'action' => 'edit'],
            ];

            foreach ($permissions as $permissionData) {
                UserPermission::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'module' => $permissionData['module'],
                        'action' => $permissionData['action']
                    ],
                    [
                        'id' => Str::uuid(),
                        'client_id' => $client->id,
                        'module' => $permissionData['module'],
                        'action' => $permissionData['action']
                    ]
                );
            }
        }
    }
}
