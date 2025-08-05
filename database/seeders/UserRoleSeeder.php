<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $roles = [
            'super-admin',
            'company-owner',
            'project-manager',
            'team-member'
        ];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
        
        // Create super admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
                'phone' => '+1234567890',
                'position' => 'System Administrator',
                'revenue_percentage' => 10.00
            ]
        );
        $adminUser->assignRole('super-admin');
        
        // Create company owner user
        $companyOwnerUser = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Company Owner',
                'password' => Hash::make('password'),
                'is_active' => true,
                'phone' => '+1987654321',
                'position' => 'CEO',
                'revenue_percentage' => 15.00
            ]
        );
        $companyOwnerUser->assignRole('company-owner');
        
        // Create project manager user
        $projectManagerUser = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Project Manager',
                'password' => Hash::make('password'),
                'is_active' => true,
                'phone' => '+1122334455',
                'position' => 'Project Manager',
                'revenue_percentage' => 8.50
            ]
        );
        $projectManagerUser->assignRole('project-manager');
        
        // Create team member user
        $teamMemberUser = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Team Member',
                'password' => Hash::make('password'),
                'is_active' => true,
                'phone' => '+1555666777',
                'position' => 'Developer',
                'revenue_percentage' => 5.25
            ]
        );
        $teamMemberUser->assignRole('team-member');
        
        // Create a company
        $company = Company::firstOrCreate(
            ['name' => 'Example Company Ltd.'],
            [
                'address' => '123 Business Street',
                'phone' => '+1999888777',
                'email' => 'info@examplecompany.com',
                'description' => 'Example company for demonstration purposes',
                'is_active' => true,
                'created_by' => $companyOwnerUser->id
            ]
        );
        
        // Create main company
        $mainCompany = Company::firstOrCreate(
            ['name' => 'Main Company'],
            [
                'address' => '456 Main Street',
                'phone' => '+1888777666',
                'email' => 'info@maincompany.com',
                'description' => 'Main company for the system',
                'is_active' => true,
                'is_main' => true,
                'created_by' => $adminUser->id
            ]
        );
        
        // Associate users with company
        $companyOwnerUser->update(['company_id' => $company->id]);
        $projectManagerUser->update(['company_id' => $company->id]);
        $teamMemberUser->update(['company_id' => $company->id]);
        
        // Create projects
        $project1 = Project::firstOrCreate(
            ['name' => 'Website Redesign'],
            [
                'company_id' => $company->id,
                'description' => 'Complete redesign of company website with modern UI/UX',
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'is_active' => true,
                'created_by' => $projectManagerUser->id,
                'responsible_user_id' => $projectManagerUser->id
            ]
        );
        
        $project2 = Project::firstOrCreate(
            ['name' => 'Mobile App Development'],
            [
                'company_id' => $company->id,
                'description' => 'Develop a mobile app for iOS and Android platforms',
                'start_date' => now()->addWeeks(2),
                'end_date' => now()->addMonths(6),
                'is_active' => true,
                'created_by' => $companyOwnerUser->id,
                'responsible_user_id' => $projectManagerUser->id
            ]
        );
        
        // Create tasks for projects
        if ($project1->tasks()->count() === 0) {
            $project1->tasks()->create([
                'title' => 'Design Mockups',
                'description' => 'Create design mockups for homepage and key sections',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => now()->addWeeks(2),
                'is_completed' => false,
                'created_by' => $projectManagerUser->id,
                'assigned_to' => $teamMemberUser->id
            ]);
            
            $project1->tasks()->create([
                'title' => 'Frontend Development',
                'description' => 'Implement HTML/CSS/JS based on approved mockups',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => now()->addWeeks(4),
                'is_completed' => false,
                'created_by' => $projectManagerUser->id,
                'assigned_to' => $teamMemberUser->id
            ]);
        }
        
        if ($project2->tasks()->count() === 0) {
            $project2->tasks()->create([
                'title' => 'Requirements Gathering',
                'description' => 'Document all app requirements and user stories',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addWeeks(3),
                'is_completed' => false,
                'created_by' => $projectManagerUser->id,
                'assigned_to' => $projectManagerUser->id
            ]);
            
            $project2->tasks()->create([
                'title' => 'UI/UX Design',
                'description' => 'Design app interfaces and user flows',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => now()->addWeeks(5),
                'is_completed' => false,
                'created_by' => $projectManagerUser->id,
                'assigned_to' => $teamMemberUser->id
            ]);
        }
    }
}
