<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create permissions
        // Company permissions
        Permission::create(['name' => 'view companies']);
        Permission::create(['name' => 'create companies']);
        Permission::create(['name' => 'edit companies']);
        Permission::create(['name' => 'delete companies']);
        
        // Project permissions
        Permission::create(['name' => 'view projects']);
        Permission::create(['name' => 'create projects']);
        Permission::create(['name' => 'edit projects']);
        Permission::create(['name' => 'delete projects']);
        
        // Task permissions
        Permission::create(['name' => 'view tasks']);
        Permission::create(['name' => 'create tasks']);
        Permission::create(['name' => 'edit tasks']);
        Permission::create(['name' => 'delete tasks']);
        Permission::create(['name' => 'assign tasks']);
        
        // Payment permissions
        Permission::create(['name' => 'view payments']);
        Permission::create(['name' => 'create payments']);
        Permission::create(['name' => 'edit payments']);
        Permission::create(['name' => 'delete payments']);
        
        // User permissions
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        
        // Create roles and assign permissions
        
        // Super Admin role - has all permissions
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());
        
        // Company Owner role
        $companyOwnerRole = Role::create(['name' => 'company-owner']);
        $companyOwnerRole->givePermissionTo([
            'view companies', 'edit companies',
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'assign tasks',
            'view payments', 'create payments', 'edit payments', 'delete payments',
            'view users', 'create users', 'edit users'
        ]);
        
        // Project Manager role
        $projectManagerRole = Role::create(['name' => 'project-manager']);
        $projectManagerRole->givePermissionTo([
            'view companies',
            'view projects', 'edit projects',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'assign tasks',
            'view payments', 'create payments', 'edit payments',
            'view users'
        ]);
        
        // Task Owner/Employee role
        $employeeRole = Role::create(['name' => 'employee']);
        $employeeRole->givePermissionTo([
            'view companies',
            'view projects',
            'view tasks', 'edit tasks',
            'view payments',
            'view users'
        ]);
        
        // Create a super admin user if none exists
        $adminUser = User::where('email', 'admin@example.com')->first();
        
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]);
        }
        
        $adminUser->assignRole('super-admin');
    }
}
