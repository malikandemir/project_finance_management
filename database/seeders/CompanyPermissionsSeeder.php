<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CompanyPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create company management permissions
        $companyPermissions = [
            'view_companies',       // Can view list of companies
            'view_company',         // Can view a specific company details
            'create_company',       // Can create new companies
            'edit_company',         // Can edit existing companies
            'delete_company',       // Can delete companies
            'restore_company',      // Can restore soft-deleted companies
            'force_delete_company', // Can permanently delete companies
            'manage_company_users', // Can manage users associated with a company
        ];
        
        foreach ($companyPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Assign permissions to roles
        $this->assignPermissionsToRoles($companyPermissions);
    }
    
    /**
     * Assign company permissions to different roles
     */
    private function assignPermissionsToRoles(array $companyPermissions): void
    {
        // Super Admin gets all permissions - only super-admin can manage companies
        $adminRole = Role::where('name', 'super-admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($companyPermissions);
        }
        
        // Other roles can only view companies, not manage them
        
        // Company Owner can only view specific companies they belong to
        $ownerRole = Role::where('name', 'company-owner')->first();
        if ($ownerRole) {
            $ownerRole->givePermissionTo([
                'view_company',
            ]);
        }
        
        // Project Manager can view specific company details
        $managerRole = Role::where('name', 'project-manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'view_company',
            ]);
        }
        
        // Team Member can only view companies they're part of
        $memberRole = Role::where('name', 'team-member')->first();
        if ($memberRole) {
            $memberRole->givePermissionTo([
                'view_company',
            ]);
        }
    }
}
