<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create user management permissions
        $userPermissions = [
            'view_users',
            'create_user',
            'edit_user',
            'delete_user',
        ];
        
        foreach ($userPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Assign permissions to admin role
        $adminRole = Role::where('name', 'super-admin')->first();
        
        if ($adminRole) {
            $adminRole->givePermissionTo($userPermissions);
        } else {
            // Create admin role if it doesn't exist
            $adminRole = Role::create(['name' => 'super-admin']);
            $adminRole->givePermissionTo($userPermissions);
        }
        
        // Create manager role with limited permissions
        $managerRole = Role::where('name', 'project-manager')->first();
        
        if ($managerRole) {
            $managerRole->givePermissionTo(['view_users']);
        } else {
            $managerRole = Role::create(['name' => 'project-manager']);
            $managerRole->givePermissionTo(['view_users']);
        }
    }
}
