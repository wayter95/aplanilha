<?php

namespace Database\Seeders;

use App\Models\ClientSubscribe;
use App\Models\UserPermission;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $clients = ClientSubscribe::all();
        
        foreach ($clients as $client) {
            $adminRole = UserRole::where('client_id', $client->id)
                ->where('name', 'Admin')
                ->first();
            
            $userRole = UserRole::where('client_id', $client->id)
                ->where('name', 'User')
                ->first();

            if ($adminRole) {
                $allPermissions = UserPermission::where('client_id', $client->id)->pluck('id');
                $adminRole->permissions()->sync($allPermissions);
            }

            if ($userRole) {
                $userPermissions = UserPermission::where('client_id', $client->id)
                    ->where(function($query) {
                        $query->where('module', 'profile')
                              ->orWhere('module', 'dashboard')
                              ->orWhere('module', 'reports');
                    })
                    ->pluck('id');
                $userRole->permissions()->sync($userPermissions);
            }
        }
    }
}
