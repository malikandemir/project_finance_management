<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use App\Filament\Resources\Components\CommentsSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\RelationManagers\CommentsRelationManager;
use App\Filament\RelationManagers\TransactionGroupsRelationManager;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    
    protected static ?string $navigationGroup = 'Project Management';
    
    protected static ?int $navigationSort = 2;
    
    public static function getModelLabel(): string
    {
        return __('filament::resources.resources.TaskResource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament::resources.resources.TaskResource.plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('filament::resources.navigation_groups.' . parent::getNavigationGroup());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament::resources.sections.task_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament::resources.fields.title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('project_id')
                            ->label(__('filament::resources.fields.project'))
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('filament::resources.fields.description'))
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label(__('filament::resources.fields.status'))
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'on_hold' => 'On Hold',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Select::make('priority')
                            ->label(__('filament::resources.fields.priority'))
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('medium')
                            ->required(),
                    ])->columns(2),
                    
                Forms\Components\Section::make(__('filament::resources.sections.schedule_assignment'))
                    ->schema([
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('filament::resources.fields.due_date')),
                        Forms\Components\Toggle::make('is_completed')
                            ->label(__('filament::resources.fields.is_completed'))
                            ->default(false),
                        Forms\Components\Select::make('assigned_to')
                            ->label(__('filament::resources.fields.responsible_person'))
                            ->relationship('assignedUser', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Placeholder::make('created_by')
                            ->label(__('filament::resources.fields.created_by'))
                            ->content(function ($record) {
                                if (!$record) {
                                    return auth()->user()->name;
                                }
                                return $record->exists && $record->creator ? $record->creator->name : (auth()->user()->name ?? 'Unknown');
                            }),
                    ])->columns(2),
                    
                Forms\Components\Section::make(__('filament::resources.sections.financial_details'))
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label(__('filament::resources.fields.price'))
                            ->numeric()
                            ->prefix('₺')
                            ->inputMode('decimal'),
                        Forms\Components\TextInput::make('cost_percentage')
                            ->label(__('filament::resources.fields.cost_percentage'))
                            ->numeric()
                            ->suffix('%')
                            ->inputMode('decimal')
                            ->maxValue(100),
                    ])->columns(2),
                    
                // Add Comments Section (only visible on edit/view pages, not on create)
                Forms\Components\Placeholder::make('comments_section')
                    ->content(fn ($record) => $record && $record->exists ? null : 'Comments will be available after saving the task.')
                    ->visible(fn ($operation) => $operation !== 'create')
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament::resources.fields.title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('filament::resources.fields.project'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament::resources.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'blue',
                        'completed' => 'green',
                        'on_hold' => 'orange',
                        'cancelled' => 'red',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('filament::resources.fields.priority'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'blue',
                        'high' => 'orange',
                        'urgent' => 'red',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('filament::resources.fields.due_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->label(__('filament::resources.fields.is_completed'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label(__('filament::resources.fields.responsible_person'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('filament::resources.fields.created_by'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('filament::resources.fields.price'))
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_percentage')
                    ->label(__('filament::resources.fields.cost_percentage'))
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament::resources.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament::resources.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label(__('filament::resources.fields.project'))
                    ->relationship('project', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament::resources.fields.status'))
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'on_hold' => 'On Hold',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->label(__('filament::resources.fields.priority'))
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
                Tables\Filters\TernaryFilter::make('is_completed')
                    ->label(__('filament::resources.fields.completed')),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label(__('filament::resources.fields.responsible_person'))
                    ->relationship('assignedUser', 'name'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
            TransactionGroupsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
