<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //


         $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // 3. asignar permisos al admin (filtrados)
        $admin->syncPermissions(
            Permission::where('name', 'like', '%View%')
                ->orWhere('name', 'like', '%Create%')
                ->orWhere('name', 'like', '%Update%')
                ->get()
        );

        $panelUser = Role::firstOrCreate([
            'name' => 'panel_user',
            'guard_name' => 'web',
        ]);

        // 3. asignar permisos al admin (filtrados)
        $panelUser->syncPermissions(
            Permission::where('name', 'like', '%View%')
                ->get()
        );
    }
}
