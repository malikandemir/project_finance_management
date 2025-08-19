<?php

namespace App\Filament\Resources\Components;

use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TasksSection
{
    public static function make(): Section
    {
        return Section::make('Tasks')
            ->schema([
                Forms\Components\Placeholder::make('tasks_list')
                    ->content(function ($record) {
                        if (!$record || !$record->exists) {
                            return 'Tasks will be available after saving the project.';
                        }
                        
                        if ($record->tasks()->count() === 0) {
                            return 'No tasks yet.';
                        }
                        
                        // Create a table-like display for tasks
                        $tasks = $record->tasks()->with('assignedUser')->get();
                        $html = '<div class="overflow-x-auto">';
                        $html .= '<table class="min-w-full divide-y divide-gray-200">';
                        
                        // Table header
                        $html .= '<thead class="bg-gray-50">';
                        $html .= '<tr>';
                        $html .= '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>';
                        $html .= '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>';
                        $html .= '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>';
                        $html .= '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>';
                        $html .= '</tr>';
                        $html .= '</thead>';
                        
                        // Table body
                        $html .= '<tbody class="bg-white divide-y divide-gray-200">';
                        foreach ($tasks as $task) {
                            $html .= '<tr>';
                            
                            // Task name with link
                            $taskUrl = route('filament.admin.resources.tasks.edit', ['record' => $task->id]);
                            $html .= '<td class="px-6 py-4 whitespace-nowrap">';
                            $html .= '<a href="' . $taskUrl . '" class="text-indigo-600 hover:text-indigo-900">' . e($task->title) . '</a>';
                            $html .= '</td>';
                            
                            // Status
                            $statusColor = match($task->status) {
                                'completed' => 'green',
                                'in_progress' => 'blue',
                                'pending' => 'yellow',
                                default => 'gray'
                            };
                            $html .= '<td class="px-6 py-4 whitespace-nowrap">';
                            $html .= '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-' . $statusColor . '-100 text-' . $statusColor . '-800">';
                            $html .= ucfirst(str_replace('_', ' ', $task->status));
                            $html .= '</span>';
                            $html .= '</td>';
                            
                            // Due date
                            $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">';
                            $html .= $task->due_date ? $task->due_date->format('M d, Y') : 'Not set';
                            $html .= '</td>';
                            
                            // Assigned user
                            $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">';
                            $html .= $task->assignedUser ? e($task->assignedUser->name) : 'Unassigned';
                            $html .= '</td>';
                            
                            $html .= '</tr>';
                        }
                        $html .= '</tbody>';
                        $html .= '</table>';
                        $html .= '</div>';
                        
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->columnSpanFull(),
                
                // Add task button
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('add_task')
                        ->label('Add Task')
                        ->url(function ($record) {
                            if (!$record || !$record->exists) {
                                return null;
                            }
                            return route('filament.admin.resources.tasks.create', ['project_id' => $record->id]);
                        })
                        ->visible(fn ($record) => $record && $record->exists)
                        ->icon('heroicon-o-plus')
                        ->color('primary')
                ])
                ->visible(fn ($record) => $record && $record->exists)
            ])
            ->collapsible();
    }
}
