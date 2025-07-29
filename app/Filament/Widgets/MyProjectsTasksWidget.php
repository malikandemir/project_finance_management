<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;

class MyProjectsTasksWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int|string|array $columnSpan = 'full';
    
    protected function getTableHeading(): string
    {
        return __('filament::widgets.my_projects_tasks.heading');
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('filament::widgets.my_projects_tasks.table_heading'))
            ->query(
                Task::query()
                    ->whereHas('project', function (Builder $query) {
                        $query->where('responsible_user_id', auth()->id());
                    })
                    ->latest()
            )
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament::widgets.fields.title'))
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('project.name')
                    ->label(__('filament::widgets.fields.project'))
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('assignedUser.name')
                    ->label(__('filament::widgets.fields.assigned_to'))
                    ->searchable()
                    ->sortable(),
                    
                BadgeColumn::make('status')
                    ->label(__('filament::widgets.fields.status'))
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                    ])
                    ->sortable(),
                    
                BadgeColumn::make('priority')
                    ->label(__('filament::widgets.fields.priority'))
                    ->colors([
                        'danger' => 'high',
                        'warning' => 'medium',
                        'success' => 'low',
                    ])
                    ->sortable(),
                    
                TextColumn::make('due_date')
                    ->label(__('filament::widgets.fields.due_date'))
                    ->date()
                    ->sortable(),
                    
                IconColumn::make('is_completed')
                    ->label(__('filament::widgets.fields.completed'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->defaultSort('due_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament::widgets.fields.status'))
                    ->options([
                        'pending' => __('filament::widgets.options.pending'),
                        'in_progress' => __('filament::widgets.options.in_progress'),
                        'completed' => __('filament::widgets.options.completed'),
                    ]),
                    
                Tables\Filters\SelectFilter::make('priority')
                    ->label(__('filament::widgets.fields.priority'))
                    ->options([
                        'high' => __('filament::widgets.options.high'),
                        'medium' => __('filament::widgets.options.medium'),
                        'low' => __('filament::widgets.options.low'),
                    ]),
                    
                Tables\Filters\Filter::make('is_completed')
                    ->query(fn (Builder $query): Builder => $query->where('is_completed', true))
                    ->label(__('filament::widgets.filters.completed_tasks')),
                    
                Tables\Filters\Filter::make('not_completed')
                    ->query(fn (Builder $query): Builder => $query->where('is_completed', false))
                    ->label(__('filament::widgets.filters.pending_tasks')),
            ]);
    }
}
