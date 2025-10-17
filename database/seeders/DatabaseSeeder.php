<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ClientSubscribeSeeder::class,
            UserRoleSeeder::class,
            UserPermissionSeeder::class,
            UserSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
