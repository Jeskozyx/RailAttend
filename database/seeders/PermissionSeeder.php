<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view roles',
            'create roles',
            'update roles',
            'delete roles',
            'view user',
            'create user',
            'update user',
            'delete user',
            'view kereta',
            'create kereta',
            'update kereta',
            'delete kereta',
            'view rangkaian',
            'create rangkaian',
            'update rangkaian',
            'delete rangkaian',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
