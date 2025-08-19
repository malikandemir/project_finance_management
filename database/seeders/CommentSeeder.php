<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to assign as comment authors
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }
        
        // Add comments to companies
        $companies = Company::all();
        foreach ($companies as $company) {
            // Add 1-3 comments per company
            $commentCount = rand(1, 3);
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::create([
                    'content' => "This is a test comment #{$i} for company {$company->name}.",
                    'commentable_id' => $company->id,
                    'commentable_type' => Company::class,
                    'user_id' => $users->random()->id,
                ]);
            }
        }
        
        // Add comments to projects
        $projects = Project::all();
        foreach ($projects as $project) {
            // Add 2-5 comments per project
            $commentCount = rand(2, 5);
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::create([
                    'content' => "This is a test comment #{$i} for project {$project->name}.",
                    'commentable_id' => $project->id,
                    'commentable_type' => Project::class,
                    'user_id' => $users->random()->id,
                ]);
            }
        }
        
        // Add comments to tasks
        $tasks = Task::all();
        foreach ($tasks as $task) {
            // Add 1-4 comments per task
            $commentCount = rand(1, 4);
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::create([
                    'content' => "This is a test comment #{$i} for task {$task->title}.",
                    'commentable_id' => $task->id,
                    'commentable_type' => Task::class,
                    'user_id' => $users->random()->id,
                ]);
            }
        }
        
        $this->command->info('Comments seeded successfully!');
    }
}
